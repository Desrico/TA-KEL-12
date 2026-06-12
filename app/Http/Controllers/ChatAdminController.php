<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Concerns\HandlesOnlineChatSessions;
use App\Models\Chat;
use App\Models\JadwalKonseling;
use App\Models\SesiKonseling;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatAdminController extends Controller
{
    use HandlesOnlineChatSessions;

    public function index(Request $request)
    {
        $user = $request->user();
        $jadwalList = $this->buildChatRoomList($this->resolveAvailableSchedules($user));
        $selectedJadwal = $this->resolveSelectedSchedule($user, $jadwalList, $request->integer('jadwal'));

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
            ->whereIn('jadwal_konseling_id', $conversationSchedules->pluck('id')->filter()->values())
            ->pluck('id')
            ->whereIn('jadwal_konseling_id', $conversationSchedules->pluck('jadwal_konseling_id')->filter()->values())
            ->pluck('jadwal_konseling_id')
            ->push($sesi->id)
            ->unique()
            ->values();

        $messages = Chat::query()
            ->with([
                'pengirim.profil',
                'pengirim.mahasiswa',
                'sesi.jadwalKonseling.mahasiswa.user',
            ])
            ->whereIn('sesi_id', $conversationSessionIds)
            ->orderBy('created_at')
            ->get()
            ->values()
            ->map(fn(Chat $chat) => $this->transformMessage($chat, $user))
            ->all();


        $isBlockedBySchedule = ! $this->canStartSessionNow($sesi);
        $isReadyToStart = ! $isBlockedBySchedule
            && ($selectedJadwal->status ?? null) === 'disetujui'
            && ! $this->isSessionActive($sesi);
        // Grant chat access only when session is active and still within 24h window
        $chatAccessGranted = $this->isSessionActive($sesi) && $this->isChatWindowOpen($sesi);
        $canStartNow = $this->canStartSessionNow($sesi);

        $hasPendingRegularSchedule = $this->hasPendingRegularSchedule($user, $selectedJadwal);

        if ($hasPendingRegularSchedule) {
            $isBlockedBySchedule = true;
            $isReadyToStart = false;
            $chatAccessGranted = false;
            $canStartNow = false;
        }

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

    private function getStudentDisplayNameForSchedule(?JadwalKonseling $jadwal): string
    {
        if (! $jadwal) {
            return 'Mahasiswa';
        }

        $mahasiswaUser = optional(optional($jadwal->mahasiswa)->user);

        $isAnonim = filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($isAnonim) {
            if ($mahasiswaUser && method_exists($mahasiswaUser, 'getAnonimDisplayName')) {
                return trim($mahasiswaUser->getAnonimDisplayName()) ?: 'Anonim';
            }

            return 'Anonim';
        }

        return $mahasiswaUser->nama ?? 'Mahasiswa';
    }

    public function start(Request $request): RedirectResponse
    {
        $jadwal = $this->resolveScheduleForCounselor($request->user(), $request->integer('jadwal_konseling_id'));

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
            $sesi = $this->resolveSessionFromRequest($request->user(), $request);

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
                'jadwalKonseling.mahasiswa.user.profil',
                'jadwalKonseling.konselor.user.profil',
            ]);

            $selectedJadwal = $sesi->jadwalKonseling;

            if (! $selectedJadwal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal chat tidak ditemukan.',
                ], 404);
            }

            $conversationSchedules = $this->resolveConversationSchedules($request->user(), $selectedJadwal);

            $conversationSessionIds = SesiKonseling::query()
                ->whereIn('jadwal_konseling_id', $conversationSchedules->pluck('id')->filter()->values())
                ->pluck('id')
                ->push($sesi->id)
                ->unique()
                ->values();

            $messages = Chat::query()
                ->with([
                    'pengirim.profil',
                    'pengirim.mahasiswa',
                    'sesi.jadwalKonseling.mahasiswa.user',
                ])
                ->whereIn('sesi_id', $conversationSessionIds)
                ->orderBy('created_at')
                ->get()
                ->values()
                ->map(fn (Chat $chat) => $this->transformMessage($chat, $request->user()))
                ->all();

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'thread_date_key' => $this->resolveThreadDateKey($sesi),
                'thread_date_label' => $this->resolveThreadDateLabel($sesi),
            ]);
        }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sesi_id' => 'required|integer',
            'pesan' => 'required|string|max:2000',
        ]);

        $request->merge(['sesi_id' => (int) $validated['sesi_id']]);
        $sesi = $this->resolveSessionFromRequest($request->user(), $request);

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

    public function update(Request $request, Chat $chat): JsonResponse
    {
        $validated = $request->validate([
            'pesan' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $sesi = $this->resolveSessionByOwnedChat($user, $chat);

        if (! $sesi || (int) $chat->pengirim_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan atau tidak bisa diedit.',
            ], 404);
        }

        if (! $this->canStartSessionNow($sesi) || $sesi->status !== 'berlangsung') {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak bisa diedit karena sesi sudah tidak aktif.',
            ], 403);
        }

        // Konselor hanya dapat mengubah pesan yang dia kirim sendiri.
        $chat->update([
            'pesan' => trim($validated['pesan']),
        ]);

        $chat->refresh()->loadMissing([
            'pengirim.profil',
            'pengirim.mahasiswa',
        ]);

        return response()->json([
            'success' => true,
            'message' => $this->transformMessage($chat, $user),
        ]);
    }

    public function destroy(Request $request, Chat $chat): JsonResponse
    {
        $user = $request->user();
        $sesi = $this->resolveSessionByOwnedChat($user, $chat);

        if (! $sesi || (int) $chat->pengirim_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan atau tidak bisa dihapus.',
            ], 404);
        }

        if (! $this->canStartSessionNow($sesi) || $sesi->status !== 'berlangsung') {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak bisa dihapus karena sesi sudah tidak aktif.',
            ], 403);
        }

        // Hapus permanen dibatasi ke pemilik pesan agar tidak mengganggu lawan bicara.
        $chat->delete();

        return response()->json([
            'success' => true,
            'deleted_id' => $chat->id,
        ]);
    }

    private function resolveAvailableSchedules(User $user): Collection
    {
        $konselorId = optional($user->konselor)->id;

        if (! $konselorId) {
            return collect();
        }

        return $this->synchronizeCandidateSchedules(
            JadwalKonseling::query()
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
                })
        );
    }

    private function buildChatRoomList(Collection $schedules): Collection
    {
        $regularRooms = $this->collapseSchedulesByStudent(
            $schedules->filter(function (JadwalKonseling $jadwal) {
                return ! filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);
            })
        );

        $anonymousRooms = $schedules
            ->filter(function (JadwalKonseling $jadwal) {
                return filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);
            })
            ->map(function (JadwalKonseling $jadwal) {
                $displayState = $this->getScheduleDisplayState($jadwal);

                $jadwal->display_status_key = $displayState['key'];
                $jadwal->display_status_label = $displayState['label'];
                $jadwal->nama_tampil = $this->getStudentDisplayNameForSchedule($jadwal);
                $jadwal->conversation_dates_label = null;

                return $jadwal;
            })
            ->values();

        return $regularRooms
            ->merge($anonymousRooms)
            ->sort(function (JadwalKonseling $left, JadwalKonseling $right) {
                $leftRank = $this->schedulePriorityRank($left);
                $rightRank = $this->schedulePriorityRank($right);

                if ($leftRank !== $rightRank) {
                    return $leftRank <=> $rightRank;
                }

                $leftAt = $left->scheduledAt();
                $rightAt = $right->scheduledAt();

                if ($leftAt && $rightAt) {
                    return $rightAt->getTimestamp() <=> $leftAt->getTimestamp();
                }

                return $right->id <=> $left->id;
            })
            ->values();
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
                    ? 'Riwayat: ' . $historyLabels->take(2)->implode(', ') . ($historyLabels->count() > 2 ? ' +' . ($historyLabels->count() - 2) . ' lagi' : '')
                    : null;

                $displayState = $this->getScheduleDisplayState($selected);
                $selected->display_status_key = $displayState['key'];
                $selected->display_status_label = $displayState['label'];
                $selected->nama_tampil = $this->getStudentDisplayNameForSchedule($selected);

                return $selected;
            })
            ->filter()
            ->values();
    }

   private function resolveConversationSchedules(User $user, JadwalKonseling $selectedJadwal): Collection
    {
        $isAnonim = filter_var($selectedJadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($isAnonim) {
            return collect([$selectedJadwal]);
        }

        $konselorId = optional($user->konselor)->id;

        return JadwalKonseling::query()
            ->with([
                'mahasiswa.user.profil',
                'konselor.user.profil',
                'sesiKonseling',
            ])
            ->where('konselor_id', $konselorId)
            ->where('mahasiswa_id', $selectedJadwal->mahasiswa_id)
            ->where('jenis', 'online')
            ->where(function ($query) {
                $query->whereNull('anonim')
                    ->orWhere('anonim', false)
                    ->orWhere('anonim', 0);
            })
            ->whereIn('status', ['disetujui', 'berlangsung', 'selesai'])
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();
    }

    private function resolveSelectedSchedule(User $user, Collection $jadwalList, ?int $jadwalId): ?JadwalKonseling
    {
        if ($jadwalId) {
            $selected = $jadwalList->firstWhere('id', $jadwalId);

            if ($selected) {
                return $selected;
            }

            $requested = $this->resolveScheduleForCounselor($user, $jadwalId);

            if ($requested) {
                $isAnonim = filter_var($requested->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

                if ($isAnonim) {
                    return $requested;
                }

                $regularRoom = $jadwalList->first(function (JadwalKonseling $jadwal) use ($requested) {
                    return ! filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN)
                        && (int) $jadwal->mahasiswa_id === (int) $requested->mahasiswa_id;
                });

                if ($regularRoom) {
                    return $regularRoom;
                }
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
            ->whereIn('status', ['disetujui', 'berlangsung', 'selesai'])
            ->find($jadwalId);
    }

    private function resolveSessionFromRequest(User $user, Request $request): ?SesiKonseling
    {
        $jadwalId = $request->integer('jadwal_konseling_id');

        if ($jadwalId) {
            $jadwal = $this->resolveScheduleForCounselor($user, $jadwalId);

            if (! $jadwal) {
                return null;
            }

            $selected = $this->resolveSelectedSchedule(
                $user,
                $this->resolveAvailableSchedules($user),
                $jadwal->id
            );

            return $selected ? $this->resolveSessionFromSchedule($selected) : null;
        }

        $sesiId = $request->integer('sesi_id');

        if (! $sesiId) {
            $selected = $this->resolveSelectedSchedule($user, $this->resolveAvailableSchedules($user), null);

            return $selected ? $this->resolveSessionFromSchedule($selected) : null;
        }

        return $this->resolveSessionByIdForCounselor($user, $sesiId);
    }

    private function resolveSessionByIdForCounselor(User $user, ?int $sesiId): ?SesiKonseling
    {
        if (! $sesiId) {
            return null;
        }

        $konselorId = optional($user->konselor)->id;

        $sesi = SesiKonseling::query()
            ->with([
                'jadwalKonseling.mahasiswa.user.profil',
                'jadwalKonseling.konselor.user.profil',
            ])
            ->whereHas('jadwalKonseling', function ($query) use ($konselorId) {
                $query->where('konselor_id', $konselorId);
            })
            ->find($sesiId);

        if (! $sesi) {
            return null;
        }

        // Resolver ini hanya membaca sesi milik konselor, tidak boleh menggeser waktu aktif atau expiry.
        return $this->synchronizeSessionState($sesi);
    }

    private function isChatWindowOpen(SesiKonseling $sesi): bool
    {
        $sesi = $this->synchronizeSessionState($sesi);

        return (bool) $sesi->jadwalKonseling?->isChatWindowOpen(null, $this->displayTimezone());
    }

    private function canStartSessionNow(SesiKonseling $sesi): bool
    {
        return $this->isChatWindowOpen($sesi);
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
            'channel' => 'chat.sesi.' . $sesi->id,
            'startUrl' => route('admin.chat.start'),
            'sendUrl' => route('admin.chat.store'),
            'messagesUrl' => route('admin.chat.messages'),
            'updateUrlTemplate' => route('admin.chat.update', ['chat' => '__CHAT_ID__']),
            'deleteUrlTemplate' => route('admin.chat.destroy', ['chat' => '__CHAT_ID__']),
            'videoCallUrl' => $this->buildVideoCallUrl($sesi),
            'status' => $jadwal->status ?? 'disetujui',
            'studentName' => $this->getStudentDisplayNameForSchedule($jadwal),
            'studentAvatar' => filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN)
                ? asset('img/default-avatar.png')
                : ($mahasiswaProfil?->foto ? Storage::url($mahasiswaProfil->foto) : asset('img/default-avatar.png')),
            'canStart' => $isReadyToStart,
            'canStartNow' => $canStartNow,
            'expiresAt' => $expiresAt?->toIso8601String(),
            'remainingSeconds' => $remainingSeconds,
            'threadDateKey' => $this->resolveThreadDateKey($sesi),
            'threadDateLabel' => $this->resolveThreadDateLabel($sesi),
            'messages' => $messages,
        ];
    }

    private function transformMessage(Chat $chat, User $viewer): array
{
    $chat->loadMissing([
        'pengirim.profil',
        'pengirim.mahasiswa',
        'sesi.jadwalKonseling.mahasiswa.user',
    ]);

    $sender = $chat->pengirim;
    $profil = optional($sender)->profil;
    $jadwal = optional($chat->sesi)->jadwalKonseling;

    $isAnonim = filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);
    $isSenderMahasiswa = ($sender?->role ?? null) === 'mahasiswa';

    if ($isSenderMahasiswa && $isAnonim) {
        $senderName = method_exists($sender, 'getAnonimDisplayName')
            ? (trim($sender->getAnonimDisplayName()) ?: 'Anonim')
            : 'Anonim';

        $avatarUrl = asset('img/default-avatar.png');
    } else {
        $senderName = $sender?->nama ?? 'Pengguna';
        $avatarUrl = $profil?->foto
            ? Storage::url($profil->foto)
            : asset('img/default-avatar.png');
    }

    $sentAt = $this->toDisplayDateTime($chat->created_at) ?? $this->nowInDisplayTimezone();

    return [
        'id' => $chat->id,
        'sesi_id' => $chat->sesi_id,
        'sender_id' => $chat->pengirim_id,
        'sender_name' => $senderName,
        'sender_role' => $sender?->role ?? 'pengguna',
        'avatar_url' => $avatarUrl,
        'text' => $chat->pesan,
        'time' => $sentAt->format('H:i'),
        'sent_at' => $sentAt->toIso8601String(),
        'date_key' => $sentAt->format('Y-m-d'),
        'date_label' => $sentAt->translatedFormat('l, j F Y'),
        'updated_at' => $this->toDisplayDateTime($chat->updated_at)?->toIso8601String(),
        'is_edited' => (bool) ($chat->updated_at && $chat->created_at && $chat->updated_at->ne($chat->created_at)),
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
        $sesi = $this->synchronizeSessionState($sesi);

        return $this->isChatWindowOpen($sesi)
            && (
                $sesi->status === 'berlangsung'
                || ($sesi->jadwalKonseling?->status === 'berlangsung')
            );
    }

    private function getScheduleBlockedMessage(SesiKonseling $sesi): string
    {
        $scheduledEndAt = $this->getScheduledEndAt($sesi);

        if ($scheduledEndAt && $this->nowInDisplayTimezone()->greaterThanOrEqualTo($scheduledEndAt)) {
            return 'Sesi konseling online ini sudah melewati batas 24 jam dan dinyatakan selesai.';
        }

        return 'Sesi konseling online ini akan dimulai pada ' . $this->getScheduledStartLabel($sesi) . '. Sebelum itu, ruang chat belum bisa diakses.';
    }

    private function buildVideoCallUrl(SesiKonseling $sesi): string
    {
        return 'https://meet.jit.si/campus-care-sesi-' . $sesi->id;
    }

    private function resolveConversationMessages(SesiKonseling $activeSession, User $viewer): Collection
{
    $activeSession->loadMissing([
        'chats.pengirim.profil',
        'chats.pengirim.mahasiswa',
    ]);
        if (! $jadwal) {
            return collect();
        }

        $schedules = JadwalKonseling::query()
            ->with([
                'sesiKonseling.chats.pengirim.profil',
                'sesiKonseling.chats.pengirim.mahasiswa',
            ])
            ->where('mahasiswa_id', $jadwal->mahasiswa_id)
            ->where('konselor_id', $jadwal->konselor_id)
            ->where('jenis', 'online')
            ->whereIn('status', ['disetujui', 'berlangsung', 'selesai'])
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();

        $messages = collect();

        foreach ($schedules as $schedule) {
            $session = null;

            if ((int) $schedule->id === (int) $jadwal->id) {
                $session = $activeSession;
            } elseif ($schedule->sesiKonseling) {
                $session = $schedule->sesiKonseling;
                $session->setRelation('jadwalKonseling', $schedule);
                $session = $this->synchronizeSessionState($session);
            }

            if (! $session) {
                continue;
            }

            $session->loadMissing([
                'chats.pengirim.profil',
                'chats.pengirim.mahasiswa',
            ]);

            $messages = $messages->merge($session->chats);
        }

        return $messages
            ->sortBy('created_at')
            ->values()
            ->map(fn(Chat $chat) => $this->transformMessage($chat, $viewer));
    }

    private function synchronizeCandidateSchedules(Collection $jadwalCollection): Collection
    {
        return $jadwalCollection
            ->map(function (JadwalKonseling $jadwal) {
                return $this->resolveSessionFromSchedule($jadwal)->jadwalKonseling;
            })
            ->filter();
    }

    private function resolveThreadDateKey(SesiKonseling $sesi): string
    {
        return $this->getScheduledAt($sesi)?->format('Y-m-d')
            ?? $this->nowInDisplayTimezone()->format('Y-m-d');
    }

    private function resolveThreadDateLabel(SesiKonseling $sesi): string
    {
        return ($this->getScheduledAt($sesi) ?? $this->nowInDisplayTimezone())
            ->translatedFormat('l, j F Y');
    }

    private function resolveSessionByOwnedChat(User $user, Chat $chat): ?SesiKonseling
    {
        $chat->loadMissing([
            'sesi.jadwalKonseling.mahasiswa.user.profil',
            'sesi.jadwalKonseling.konselor.user.profil',
        ]);

        $sesi = $chat->sesi;

        if (! $sesi) {
            return null;
        }

        if ((int) optional(optional($sesi->jadwalKonseling)->konselor)->user_id !== (int) $user->id) {
            return null;
        }

        return $this->synchronizeSessionState($sesi);
    }

    private function hasPendingRegularSchedule(User $user, JadwalKonseling $selectedJadwal): bool
    {
        $isAnonim = filter_var($selectedJadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($isAnonim) {
            return false;
        }

        $konselorId = optional($user->konselor)->id;

        return JadwalKonseling::query()
            ->where('konselor_id', $konselorId)
            ->where('mahasiswa_id', $selectedJadwal->mahasiswa_id)
            ->where('jenis', 'online')
            ->where(function ($query) {
                $query->whereNull('anonim')
                    ->orWhere('anonim', false)
                    ->orWhere('anonim', 0);
            })
            ->where('status', 'menunggu')
            ->exists();
    }
}
