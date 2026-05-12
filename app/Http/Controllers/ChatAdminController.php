<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\Chat;
use App\Models\JadwalKonseling;
use App\Models\SesiKonseling;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $availableSchedules = $this->resolveAvailableSchedules($user);
        $jadwalList = $this->collapseSchedulesByStudent($availableSchedules);
        $selectedJadwal = $this->resolveSelectedSchedule($jadwalList, $request->integer('jadwal'));

        if (! $selectedJadwal) {
            return view('admin.chat', [
                'jadwalList' => collect(),
                'activeJadwal' => null,
                'activeSession' => null,
                'isBlockedBySchedule' => false,
                'isReadyToStart' => false,
                'chatAccessGranted' => false,
                'canStartNow' => false,
                'scheduledStartLabel' => null,
                'chatPayload' => null,
            ]);
        }

        $sesi = $this->resolveSessionFromSchedule($selectedJadwal);
        $sesi->loadMissing([
            'jadwalKonseling.konselor.user.profil',
            'jadwalKonseling.mahasiswa.user.profil',
            'chats.pengirim.profil',
            'chats.pengirim.mahasiswa',
        ]);

        $conversationSchedules = $this->resolveConversationSchedules($user, $selectedJadwal);
        $conversationSessionIds = SesiKonseling::query()
            ->whereIn('jadwal_id', $conversationSchedules->pluck('id')->filter()->values())
            ->pluck('id')
            ->push($sesi->id)
            ->unique()
            ->values();

        $messages = Chat::query()
            ->with([
                'pengirim.profil',
                'pengirim.mahasiswa',
            ])
            ->whereIn('sesi_id', $conversationSessionIds)
            ->orderBy('created_at')
            ->get()
            ->values()
            ->map(fn (Chat $chat) => $this->transformMessage($chat, $user))
            ->all();

        $isBlockedBySchedule = ! $this->canStartSessionNow($sesi);
        $isReadyToStart = ! $isBlockedBySchedule
            && ($selectedJadwal->status ?? null) === 'disetujui'
            && ! $this->isSessionActive($sesi);
        // Grant chat access only when session is active and still within 24h window
        $chatAccessGranted = $this->isSessionActive($sesi) && $this->isChatWindowOpen($sesi);
        $canStartNow = $this->canStartSessionNow($sesi);

        return view('admin.chat', [
            'jadwalList' => $jadwalList,
            'activeJadwal' => $selectedJadwal,
            'activeSession' => $sesi,
            'isBlockedBySchedule' => $isBlockedBySchedule,
            'isReadyToStart' => $isReadyToStart,
            'chatAccessGranted' => $chatAccessGranted,
            'canStartNow' => $canStartNow,
            'scheduledStartLabel' => $this->getScheduledStartLabel($sesi),
            'chatPayload' => $this->buildChatPayload($sesi, $messages, $isReadyToStart, $canStartNow),
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        $jadwal = $this->resolveScheduleForCounselor($request->user(), $request->integer('jadwal_id'));

        if (! $jadwal) {
            return redirect()
                ->route('admin.chat')
                ->with('error', 'Jadwal sesi tidak ditemukan.');
        }

        $sesi = $this->resolveSessionFromSchedule($jadwal);

        if (! $this->canStartSessionNow($sesi)) {
            return redirect()
                ->route('admin.chat', ['jadwal' => $jadwal->id])
                ->with('error', $this->getScheduleBlockedMessage($sesi));
        }

        if ($this->isSessionActive($sesi)) {
            return redirect()->route('admin.chat', ['jadwal' => $jadwal->id]);
        }

        if (($jadwal->status ?? null) !== 'disetujui') {
            return redirect()
                ->route('admin.chat', ['jadwal' => $jadwal->id])
                ->with('error', 'Sesi belum siap dimulai.');
        }

        $this->activateSessionIfNeeded($sesi);

        return redirect()
            ->route('admin.chat', ['jadwal' => $jadwal->id])
            ->with('success', 'Sesi chat dengan mahasiswa berhasil dimulai.');
    }

    public function messages(Request $request): JsonResponse
    {
        $sesi = $this->resolveSessionByIdForCounselor($request->user(), $request->integer('sesi_id'));

        if (! $sesi) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi chat tidak ditemukan.',
            ], 404);
        }

        if (! $this->isChatWindowOpen($sesi) || $sesi->status !== 'berlangsung') {
            return response()->json([
                'success' => false,
                'message' => 'Ruang chat tidak tersedia (sudah kadaluarsa atau belum dimulai).',
            ], 403);
        }

        $sesi->loadMissing([
            'chats.pengirim.profil',
            'chats.pengirim.mahasiswa',
        ]);

        return response()->json([
            'success' => true,
            'messages' => $sesi->chats
                ->sortBy('created_at')
                ->values()
                ->map(fn (Chat $chat) => $this->transformMessage($chat, $request->user()))
                ->all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sesi_id' => 'required|integer',
            'pesan' => 'required|string|max:2000',
        ]);

        $sesi = $this->resolveSessionByIdForCounselor($request->user(), (int) $validated['sesi_id']);

        if (! $sesi) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi chat tidak ditemukan.',
            ], 404);
        }

        if (! $this->isChatWindowOpen($sesi) || $sesi->status !== 'berlangsung') {
            return response()->json([
                'success' => false,
                'message' => 'Ruang chat tidak tersedia (sudah kadaluarsa atau belum dimulai).',
            ], 403);
        }

        $chat = DB::transaction(function () use ($request, $validated, $sesi) {
            return Chat::create([
                'sesi_id' => $sesi->id,
                'pengirim_id' => $request->user()->id,
                'pesan' => trim($validated['pesan']),
            ]);
        });

        $chat->loadMissing([
            'pengirim.profil',
            'pengirim.mahasiswa',
            'sesi.jadwalKonseling',
        ]);

        try {
            broadcast(new ChatMessageSent($chat))->toOthers();
        } catch (\Throwable $exception) {
            Log::warning('Broadcast chat admin gagal dikirim ke websocket.', [
                'chat_id' => $chat->id,
                'sesi_id' => $chat->sesi_id,
                'error' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $this->transformMessage($chat, $request->user()),
        ]);
    }

    private function resolveAvailableSchedules(User $user): Collection
    {
        $konselorId = optional($user->konselor)->id;

        if (! $konselorId) {
            return collect();
        }

        return JadwalKonseling::query()
            ->with([
                'mahasiswa.user.profil',
                'konselor.user.profil',
                'sesiKonseling',
            ])
            ->where('konselor_id', $konselorId)
                ->whereIn('status', ['disetujui', 'berlangsung', 'selesai'])
            ->orderByRaw("
                CASE
                    WHEN status = 'berlangsung' THEN 1
                    WHEN status = 'disetujui' THEN 2
                        WHEN status = 'selesai' THEN 3
                        ELSE 4
                END
            ")
            ->orderBy('tanggal')
            ->orderBy('waktu')
                ->get()
                ->each(function (JadwalKonseling $jadwal) {
                    $jadwal->syncExpiredSessionStatus();
                });
    }

    private function collapseSchedulesByStudent(Collection $schedules): Collection
    {
        return $schedules
            ->groupBy('mahasiswa_id')
            ->map(function (Collection $group) {
                $sorted = $group->sort(function (JadwalKonseling $left, JadwalKonseling $right) {
                    $leftRank = $this->schedulePriorityRank($left);
                    $rightRank = $this->schedulePriorityRank($right);

                    if ($leftRank !== $rightRank) {
                        return $leftRank <=> $rightRank;
                    }

                    $leftAt = $left->scheduledAt();
                    $rightAt = $right->scheduledAt();

                    if ($leftAt && $rightAt) {
                        return $leftAt->getTimestamp() <=> $rightAt->getTimestamp();
                    }

                    if ($leftAt) {
                        return -1;
                    }

                    if ($rightAt) {
                        return 1;
                    }

                    return $left->id <=> $right->id;
                })->values();

                $selected = $sorted->first();

                if (! $selected) {
                    return null;
                }

                $historyLabels = $sorted
                    ->map(function (JadwalKonseling $item) {
                        $scheduledAt = $item->scheduledAt();

                        if (! $scheduledAt) {
                            return null;
                        }

                        return $scheduledAt->translatedFormat('j M Y');
                    })
                    ->filter()
                    ->unique()
                    ->values();

                $selected->conversation_dates_label = $historyLabels->isNotEmpty()
                    ? 'Riwayat: '.$historyLabels->take(2)->implode(', ').($historyLabels->count() > 2 ? ' +'.($historyLabels->count() - 2).' lagi' : '')
                    : null;

                $displayState = $this->getScheduleDisplayState($selected);
                $selected->display_status_key = $displayState['key'];
                $selected->display_status_label = $displayState['label'];

                return $selected;
            })
            ->filter()
            ->values();
    }

    private function resolveConversationSchedules(User $user, JadwalKonseling $selectedJadwal): Collection
    {
        $konselorId = optional($user->konselor)->id;

        if (! $konselorId || ! $selectedJadwal->mahasiswa_id) {
            return collect([$selectedJadwal]);
        }

        $conversationSchedules = JadwalKonseling::query()
            ->with([
                'mahasiswa.user.profil',
                'konselor.user.profil',
                'sesiKonseling',
            ])
            ->where('konselor_id', $konselorId)
            ->where('mahasiswa_id', $selectedJadwal->mahasiswa_id)
            ->where(function ($query) {
                $query->where('jenis', 'online')
                    ->orWhereNull('jenis');
            })
            ->whereIn('status', ['disetujui', 'berlangsung', 'selesai'])
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();

        if ($conversationSchedules->isEmpty()) {
            return collect([$selectedJadwal]);
        }

        return $conversationSchedules;
    }

    private function resolveSelectedSchedule(Collection $jadwalList, ?int $jadwalId): ?JadwalKonseling
    {
        if ($jadwalId) {
            $selected = $jadwalList->firstWhere('id', $jadwalId);

            if ($selected) {
                return $selected;
            }
        }

        return $jadwalList->first();
    }

    private function resolveScheduleForCounselor(User $user, ?int $jadwalId): ?JadwalKonseling
    {
        if (! $jadwalId) {
            return null;
        }

        $konselorId = optional($user->konselor)->id;

        return JadwalKonseling::query()
            ->with([
                'mahasiswa.user.profil',
                'konselor.user.profil',
            ])
            ->where('konselor_id', $konselorId)
            ->whereIn('status', ['disetujui', 'berlangsung'])
            ->find($jadwalId);
    }

    private function resolveSessionFromSchedule(JadwalKonseling $jadwal): SesiKonseling
    {
        $sesi = SesiKonseling::firstOrCreate(
            ['jadwal_id' => $jadwal->id],
            [
                'status' => $jadwal->status === 'berlangsung' ? 'berlangsung' : 'disetujui',
            ]
        );

        $sesi->setRelation('jadwalKonseling', $jadwal);

        return $sesi;
    }

    private function resolveSessionByIdForCounselor(User $user, ?int $sesiId): ?SesiKonseling
    {
        if (! $sesiId) {
            return null;
        }

        $konselorId = optional($user->konselor)->id;

        return SesiKonseling::query()
            ->with([
                'jadwalKonseling.mahasiswa.user.profil',
                'jadwalKonseling.konselor.user.profil',
                'chats.pengirim.profil',
                'chats.pengirim.mahasiswa',
            ])
            ->whereHas('jadwalKonseling', function ($query) use ($konselorId) {
                $query->where('konselor_id', $konselorId);
            })
            ->find($sesiId);
    }

    private function activateSessionIfNeeded(SesiKonseling $sesi): void
    {
        if (! $this->isSessionActive($sesi)) {
            $sesi->forceFill([
                'status' => 'berlangsung',
            ])->save();
        }

        $jadwal = $sesi->jadwalKonseling;

        if ($jadwal && $jadwal->status !== 'berlangsung') {
            $jadwal->forceFill([
                'status' => 'berlangsung',
                'started_at' => Carbon::now($this->displayTimezone()),
                'expires_at' => Carbon::now($this->displayTimezone())->addDay(),
            ])->save();
        }
    }

    private function isChatWindowOpen(SesiKonseling $sesi): bool
    {
        $jadwal = $sesi->jadwalKonseling;

        if (! $jadwal) {
            return false;
        }

        $now = $this->nowInDisplayTimezone();

        $start = $jadwal->startedAt() ?? $this->getScheduledAt($sesi);

        if (! $start) {
            return false;
        }

        $expires = $jadwal->expiresAt() ?? $start->copy()->addDay();

        return $now->greaterThanOrEqualTo($start) && $now->lessThanOrEqualTo($expires);
    }

    private function canStartSessionNow(SesiKonseling $sesi): bool
    {
        $scheduledAt = $this->getScheduledAt($sesi);

        if (! $scheduledAt) {
            return false;
        }

        return $this->nowInDisplayTimezone()->greaterThanOrEqualTo($scheduledAt);
    }

    private function getScheduledStartLabel(SesiKonseling $sesi): string
    {
        $scheduledAt = $this->getScheduledAt($sesi);

        if (! $scheduledAt) {
            return 'jadwal yang ditentukan';
        }

        return $scheduledAt
            ->translatedFormat('j F Y \\p\\u\\k\\u\\l H:i');
    }

    private function buildChatPayload(SesiKonseling $sesi, array $messages, bool $isReadyToStart, bool $canStartNow): array
    {
        $jadwal = $sesi->jadwalKonseling;
        $mahasiswaUser = optional(optional($jadwal)->mahasiswa)->user;
        $mahasiswaProfil = optional($mahasiswaUser)->profil;

        $expiresAt = $jadwal?->expiresAt();
        $remainingSeconds = null;
        if ($expiresAt) {
            $remainingSeconds = max(0, $expiresAt->diffInSeconds($this->nowInDisplayTimezone())) * 1;
        }

        return [
            'sessionId' => $sesi->id,
            'jadwalId' => $jadwal?->id,
            'channel' => 'chat.sesi.'.$sesi->id,
            'startUrl' => route('admin.chat.start'),
            'sendUrl' => route('admin.chat.store'),
            'messagesUrl' => route('admin.chat.messages'),
            'videoCallUrl' => $this->buildVideoCallUrl($sesi),
            'status' => $jadwal->status ?? 'disetujui',
            'studentName' => $mahasiswaUser?->getNamaDisplay() ?? 'Mahasiswa',
            'studentAvatar' => $mahasiswaProfil?->foto ? Storage::url($mahasiswaProfil->foto) : asset('img/default-avatar.png'),
            'canStart' => $isReadyToStart,
            'canStartNow' => $canStartNow,
            'expiresAt' => $expiresAt?->toIso8601String(),
            'remainingSeconds' => $remainingSeconds,
            'messages' => $messages,
        ];
    }

    private function transformMessage(Chat $chat, User $viewer): array
    {
        $chat->loadMissing([
            'pengirim.profil',
            'pengirim.mahasiswa',
        ]);

        $sender = $chat->pengirim;
        $profil = optional($sender)->profil;

        $sentAt = $this->toDisplayDateTime($chat->created_at) ?? $this->nowInDisplayTimezone();

        return [
            'id' => $chat->id,
            'sesi_id' => $chat->sesi_id,
            'sender_id' => $chat->pengirim_id,
            'sender_name' => $sender?->getNamaDisplay() ?? 'Pengguna',
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => $profil?->foto ? Storage::url($profil->foto) : asset('img/default-avatar.png'),
            'text' => $chat->pesan,
            'time' => $sentAt->format('H:i'),
            'sent_at' => $sentAt->toIso8601String(),
            'date_key' => $sentAt->format('Y-m-d'),
            'date_label' => $sentAt->translatedFormat('l, j F Y'),
            'is_mine' => $chat->pengirim_id === $viewer->id,
        ];
    }

    private function schedulePriorityRank(JadwalKonseling $jadwal): int
    {
        $status = $this->getScheduleDisplayState($jadwal)['key'];

        if ($status === 'berlangsung') {
            return 0;
        }

        if ($status === 'disetujui') {
            return $jadwal->hasScheduledTimeStarted() ? 1 : 2;
        }

        if ($status === 'selesai') {
            return 3;
        }

        return 4;
    }

    private function getScheduleDisplayState(JadwalKonseling $jadwal): array
    {
        $now = $this->nowInDisplayTimezone();
        $scheduledAt = $jadwal->scheduledAt();
        $startedAt = $jadwal->startedAt();
        $expiresAt = $jadwal->expiresAt();
        $rawStatus = strtolower((string) ($jadwal->status ?? ''));

        if ($jadwal->relationLoaded('sesiKonseling') && $jadwal->sesiKonseling?->status === 'berlangsung') {
            return [
                'key' => 'berlangsung',
                'label' => 'Berlangsung',
            ];
        }

        if ($rawStatus === 'selesai') {
            return [
                'key' => 'selesai',
                'label' => 'Selesai',
            ];
        }

        if ($rawStatus === 'ditolak' || $rawStatus === 'dibatalkan') {
            return [
                'key' => 'dibatalkan',
                'label' => 'Dibatalkan',
            ];
        }

        if ($expiresAt && $now->greaterThan($expiresAt)) {
            return [
                'key' => 'selesai',
                'label' => 'Selesai',
            ];
        }

        if ($rawStatus === 'berlangsung' || ($startedAt && $now->greaterThanOrEqualTo($startedAt))) {
            return [
                'key' => 'berlangsung',
                'label' => 'Berlangsung',
            ];
        }

        if ($rawStatus === 'disetujui' || ($scheduledAt && $now->lessThan($scheduledAt))) {
            return [
                'key' => 'disetujui',
                'label' => 'Disetujui',
            ];
        }

        return [
            'key' => $rawStatus ?: 'disetujui',
            'label' => ucfirst($rawStatus ?: 'disetujui'),
        ];
    }

    private function isSessionActive(SesiKonseling $sesi): bool
    {
        return $sesi->status === 'berlangsung';
    }

    private function getScheduleBlockedMessage(SesiKonseling $sesi): string
    {
        return 'Sesi konseling online ini akan dimulai pada '.$this->getScheduledStartLabel($sesi).'. Sebelum itu, ruang chat belum bisa diakses.';
    }

    private function buildVideoCallUrl(SesiKonseling $sesi): string
    {
        return 'https://meet.jit.si/campus-care-sesi-'.$sesi->id;
    }

    private function getScheduledAt(SesiKonseling $sesi): ?Carbon
    {
        $jadwal = $sesi->jadwalKonseling;

        if (! $jadwal || ! $jadwal->tanggal || ! $jadwal->waktu) {
            return null;
        }

        return Carbon::parse(trim($jadwal->tanggal.' '.$jadwal->waktu), $this->displayTimezone());
    }

    private function toDisplayDateTime($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        return $value instanceof Carbon
            ? $value->copy()->timezone($this->displayTimezone())
            : Carbon::parse($value)->timezone($this->displayTimezone());
    }

    private function nowInDisplayTimezone(): Carbon
    {
        return Carbon::now($this->displayTimezone());
    }

    private function displayTimezone(): string
    {
        return 'Asia/Jakarta';
    }
}
