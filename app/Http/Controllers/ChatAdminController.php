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
        $jadwalList = $this->resolveAvailableSchedules($user);
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

        $messages = $sesi->chats
            ->sortBy('created_at')
            ->values()
            ->map(fn (Chat $chat) => $this->transformMessage($chat, $user))
            ->all();

        $isBlockedBySchedule = ! $this->canStartSessionNow($sesi);
        $isReadyToStart = ! $isBlockedBySchedule
            && ($selectedJadwal->status ?? null) === 'disetujui'
            && ! $this->isSessionActive($sesi);
        $chatAccessGranted = ! $isBlockedBySchedule && $this->isSessionActive($sesi);
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

        if (! $this->canStartSessionNow($sesi)) {
            return response()->json([
                'success' => false,
                'message' => $this->getScheduleBlockedMessage($sesi),
            ], 403);
        }

        if ($sesi->status !== 'berlangsung') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi belum dimulai.',
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

        if (! $this->canStartSessionNow($sesi)) {
            return response()->json([
                'success' => false,
                'message' => $this->getScheduleBlockedMessage($sesi),
            ], 403);
        }

        if ($sesi->status !== 'berlangsung') {
            return response()->json([
                'success' => false,
                'message' => 'Mulai sesi terlebih dahulu sebelum mengirim pesan.',
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
            ])
            ->where('konselor_id', $konselorId)
            ->where('jenis', 'online')
            ->whereIn('status', ['disetujui', 'berlangsung'])
            ->orderByRaw("
                CASE
                    WHEN status = 'berlangsung' THEN 1
                    WHEN status = 'disetujui' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();
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
            ->where('jenis', 'online')
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
            ])->save();
        }
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

        return [
            'id' => $chat->id,
            'sesi_id' => $chat->sesi_id,
            'sender_id' => $chat->pengirim_id,
            'sender_name' => $chat->pengirim_id === $viewer->id ? 'Anda' : ($sender?->getNamaDisplay() ?? 'Pengguna'),
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => $profil?->foto ? Storage::url($profil->foto) : asset('img/default-avatar.png'),
            'text' => $chat->pesan,
            'time' => $this->toDisplayDateTime($chat->created_at)?->format('H:i') ?? $this->nowInDisplayTimezone()->format('H:i'),
            'sent_at' => $this->toDisplayDateTime($chat->created_at)?->toIso8601String() ?? $this->nowInDisplayTimezone()->toIso8601String(),
            'is_mine' => $chat->pengirim_id === $viewer->id,
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
