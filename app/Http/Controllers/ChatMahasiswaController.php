<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Concerns\HandlesOnlineChatSessions;
use App\Models\Chat;
use App\Models\JadwalKonseling;
use App\Models\Mahasiswa;
use App\Models\SesiKonseling;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ChatMahasiswaController extends Controller
{
    use HandlesOnlineChatSessions;

    public function index(Request $request)
    {
        $user = $request->user();

        $requestedJadwalId = $request->integer('jadwal') ?: $request->integer('jadwal_id');

        $sesi = $this->resolveActiveSession($user, $requestedJadwalId);

        if (! $sesi) {
            return view('Pages.chat', [
                'activeSession' => null,
                'chatMessages' => [],
                'chatPayload' => null,
            ]);
        }

        $sesi->loadMissing([
            'jadwalKonseling.konselor.user.profil',
            'jadwalKonseling.mahasiswa.user.profil',
        ]);

        if (
            $this->canStartSessionNow($sesi)
            && ($sesi->jadwalKonseling->status ?? null) === 'disetujui'
            && ! $this->isSessionActive($sesi)
        ) {
            $this->activateSessionIfNeeded($sesi);
            $sesi->refresh()->load([
                'jadwalKonseling.konselor.user.profil',
                'jadwalKonseling.mahasiswa.user.profil',
                'chats.pengirim.profil',
                'chats.pengirim.mahasiswa',
            ]);
        }

        $isBlockedBySchedule = ! $this->canStartSessionNow($sesi);
        $isReadyToStart = ! $isBlockedBySchedule
            && ($sesi->jadwalKonseling->status ?? null) === 'disetujui'
            && $sesi->status !== 'berlangsung';
        $chatAccessGranted = ! $isBlockedBySchedule && $this->isSessionActive($sesi);
        $messages = $this->resolveConversationMessages($sesi, $user)->all();

        $chatSelesai = $this->isReadOnlyChat($sesi);

        if ($chatSelesai) {
            $isBlockedBySchedule = false;
            $isReadyToStart = false;
            $chatAccessGranted = false;
        }

        return view('Pages.chat', [
            'activeSession' => $sesi,
            'isBlockedBySchedule' => $isBlockedBySchedule,
            'isReadyToStart' => $isReadyToStart,
            'chatAccessGranted' => $chatAccessGranted,
            'canStartNow' => $this->canStartSessionNow($sesi),
            'scheduledStartLabel' => $this->getScheduledStartLabel($sesi),
            'chatMessages' => $messages,
            'chatPayload' => $this->buildChatPayload($sesi, $messages, $isReadyToStart),
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        $jadwalId = $request->integer('jadwal_id');

        if ($jadwalId) {
            $jadwal = JadwalKonseling::query()
                ->with([
                    'konselor.user.profil',
                    'mahasiswa.user.profil',
                ])
                ->find($jadwalId);

            if (! $jadwal || $jadwal->mahasiswa?->user?->id !== $request->user()->id) {
                return redirect()
                    ->route('mahasiswa.chat')
                    ->with('error', 'Undangan sesi tidak ditemukan.');
            }

            $sesi = $this->resolveSessionFromSchedule($jadwal);

            if (! $this->canStartSessionNow($sesi)) {
                return redirect()
                    ->route('mahasiswa.chat')
                    ->with('error', $this->getScheduleBlockedMessage($sesi));
            }

            if (! $this->isSessionActive($sesi)) {
                $this->activateSessionIfNeeded($sesi);
            }
        }

        $sesi = $this->resolveActiveSession($request->user());
        $sesi = $this->resolveActiveSession($request->user(), $request->integer('jadwal_id'));

        if (! $sesi) {
            return redirect()
                ->route('mahasiswa.chat')
                ->with('error', 'Belum ada sesi konseling online yang bisa dimulai.');
        }

        $jadwal = $sesi->jadwalKonseling;

        if (! $this->canStartSessionNow($sesi)) {
            return redirect()
                ->route('mahasiswa.chat', ['jadwal' => $jadwal?->id])
                ->with('error', $this->getScheduleBlockedMessage($sesi));
        }

        if ($this->isSessionActive($sesi)) {
            return redirect()->route('mahasiswa.chat', ['jadwal' => $jadwal?->id]);
        }

        if (($jadwal->status ?? null) !== 'disetujui') {
            return redirect()
                ->route('mahasiswa.chat', ['jadwal' => $jadwal?->id])
                ->with('error', 'Sesi belum siap dimulai.');
        }

        $this->activateSessionIfNeeded($sesi);
    }

    public function messages(Request $request): JsonResponse
    {
        $user = $request->user();
        $sesi = $this->resolveSessionFromRequest($user, $request);

        if (! $sesi) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada sesi konseling online aktif.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'messages' => $this->resolveConversationMessages($sesi, $user)->all(),
            'thread_date_key' => $this->resolveThreadDateKey($sesi),
            'thread_date_label' => $this->resolveThreadDateLabel($sesi),
        ]);
    }

    public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'sesi_id' => 'nullable|integer',
        'jadwal_id' => 'nullable|integer',
        'pesan' => 'required|string|max:2000',
        'reply_to_id' => 'nullable|integer',
    ]);

    $user = $request->user();
    $sesi = $this->resolveSessionFromRequest($user, $request);

    if (! $sesi) {
        return response()->json([
            'success' => false,
            'message' => 'Belum ada sesi konseling online aktif.',
        ], 404);
    }

    $sesi->load('jadwalKonseling');

    $readOnlyChat = $this->isReadOnlyChat($sesi);

    if ($readOnlyChat) {
        return response()->json([
            'success' => false,
            'message' => 'Sesi konseling sudah selesai atau telah melewati batas waktu. Anda tidak dapat mengirim pesan lagi.',
        ], 403);
    }

    if (! $this->canStartSessionNow($sesi)) {
        return response()->json([
            'success' => false,
            'message' => $this->getScheduleBlockedMessage($sesi),
        ], 403);
    }

    if (($sesi->jadwalKonseling->status ?? null) === 'disetujui' && ! $this->isSessionActive($sesi)) {
        $this->activateSessionIfNeeded($sesi);
        $sesi->refresh()->load('jadwalKonseling');
    }

    if ($sesi->status !== 'berlangsung') {
        return response()->json([
            'success' => false,
            'message' => 'Sesi belum dimulai.',
        ], 403);
    }

    $replyToChat = $this->resolveReplyChatForSession($sesi, $validated['reply_to_id'] ?? null);

    $chat = DB::transaction(function () use ($validated, $sesi, $user, $replyToChat) {
        return Chat::create([
            'sesi_id' => $sesi->id,
            'pengirim_id' => $user->id,
            'pesan' => trim($validated['pesan']),
            'reply_to_chat_id' => $replyToChat?->id,
        ]);
    });

    $chat->loadMissing([
        'pengirim.profil',
        'pengirim.mahasiswa',
        'replyTo.pengirim',
        'sesi.jadwalKonseling',
    ]);

    try {
        broadcast(new ChatMessageSent($chat))->toOthers();
    } catch (\Throwable $exception) {
        Log::warning('Broadcast chat mahasiswa gagal dikirim ke websocket.', [
            'chat_id' => $chat->id,
            'sesi_id' => $chat->sesi_id,
            'error' => $exception->getMessage(),
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => $this->transformMessage($chat, $user),
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

        // Edit dibatasi pada pesan milik sendiri agar riwayat percakapan tetap aman.
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

        // Hapus permanen hanya diizinkan untuk pesan milik sendiri.
        $chat->delete();

        return response()->json([
            'success' => true,
            'deleted_id' => $chat->id,
        ]);
    }

    private function resolveReplyChatForSession(SesiKonseling $sesi, mixed $replyToId): ?Chat
    {
        $replyToId = (int) $replyToId;

        if ($replyToId <= 0) {
            return null;
        }

        return Chat::query()
            ->where('sesi_id', $sesi->id)
            ->find($replyToId);
    }

    private function resolveActiveSession(?User $user, ?int $jadwalId = null): ?SesiKonseling
    {
        if (! $user) {
            return null;
        }

        $mahasiswaId = Mahasiswa::query()
            ->where('user_id', $user->id)
            ->value('id');

        if (! $mahasiswaId) {
            return null;
        }

        $jadwal = $this->resolvePreferredScheduleForStudent($user, $mahasiswaId, $jadwalId);

        if (! $jadwal) {
            return null;
        }

        $sesi = $this->resolveSessionFromSchedule($jadwal);

        if ($sesi->status === 'dibatalkan') {
            return null;
        }

        return $sesi;
    }

    private function resolveScheduleForStudent(User $user, ?int $jadwalId): ?JadwalKonseling
    {
        if (! $jadwalId) {
            return null;
        }

        $mahasiswaId = Mahasiswa::query()
            ->where('user_id', $user->id)
            ->value('id');

        if (! $mahasiswaId) {
            return null;
        }

        return JadwalKonseling::query()
            ->with([
                'konselor.user.profil',
                'mahasiswa.user.profil',
            ])
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('jenis', 'online')
            ->whereIn('status', ['disetujui', 'berlangsung', 'selesai'])
            ->find($jadwalId);
    }

    private function resolveSessionFromRequest(User $user, Request $request): ?SesiKonseling
    {
        $jadwalId = $request->integer('jadwal_id');

        if ($jadwalId) {
            return $this->resolveActiveSession($user, $jadwalId);
        }

        $sesiId = $request->integer('sesi_id');

        if (! $sesiId) {
            return $this->resolveActiveSession($user);
        }

        $sesi = SesiKonseling::query()
            ->with([
                'jadwalKonseling.konselor.user.profil',
                'jadwalKonseling.mahasiswa.user.profil',
            ])
            ->whereHas('jadwalKonseling', function ($query) use ($user) {
                $query->where('mahasiswa_id', optional($user->mahasiswa)->id);
            })
            ->find($sesiId);

        if (! $sesi) {
            return null;
        }

        return $this->synchronizeSessionState($sesi);
    }

   private function resolvePreferredScheduleForStudent(User $user, int $mahasiswaId, ?int $requestedJadwalId = null): ?JadwalKonseling
    {
        $requestedSchedule = $requestedJadwalId
            ? $this->resolveScheduleForStudent($user, $requestedJadwalId)
            : null;

        if ($requestedSchedule) {
            return $requestedSchedule;
        }

        $query = JadwalKonseling::query()
            ->with([
                'konselor.user.profil',
                'mahasiswa.user.profil',
                'sesiKonseling',
            ])
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('jenis', 'online')
            ->whereIn('status', ['disetujui', 'berlangsung', 'selesai']);

        $candidates = $this->synchronizeCandidateSchedules($query->get())
            ->filter()
            ->values();

        if ($candidates->isEmpty()) {
            return null;
        }

        // Prioritas pertama: sesi yang masih aktif atau masih bisa berjalan.
        $activeSchedule = $candidates
            ->reject(function (JadwalKonseling $jadwal) {
                $status = strtolower(str_replace(' ', '_', $jadwal->status ?? ''));

                return in_array($status, ['selesai', 'ditolak', 'dibatalkan'], true);
            })
            ->sort(fn (JadwalKonseling $left, JadwalKonseling $right) => $left->compareSessionPriority($right))
            ->first();

        if ($activeSchedule) {
            return $activeSchedule;
        }

        // Jika tidak ada sesi aktif, tampilkan riwayat chat online terakhir sebagai read-only.
        return $candidates
            ->sortByDesc(function (JadwalKonseling $jadwal) {
                try {
                    return Carbon::parse($jadwal->tanggal . ' ' . substr((string) $jadwal->waktu, 0, 5))->timestamp;
                } catch (\Throwable $e) {
                    return 0;
                }
            })
            ->first();
    }

    private function isSessionExpiredForChat(SesiKonseling $sesi): bool
    {
        $scheduledAt = $this->getScheduledAt($sesi);

        if (! $scheduledAt && $sesi->jadwalKonseling) {
            try {
                $tanggal = $sesi->jadwalKonseling->tanggal;
                $waktu = substr((string) $sesi->jadwalKonseling->waktu, 0, 5);

                $scheduledAt = Carbon::parse($tanggal . ' ' . $waktu, 'Asia/Jakarta');
            } catch (\Throwable $e) {
                $scheduledAt = null;
            }
        }

        if (! $scheduledAt) {
            return false;
        }

        return $this->nowInDisplayTimezone()->gte($scheduledAt->copy()->addHours(24));
    }

    private function isReadOnlyChat(SesiKonseling $sesi): bool
    {
        $statusSesi = strtolower(str_replace(' ', '_', $sesi->status ?? ''));
        $statusJadwal = strtolower(str_replace(' ', '_', $sesi->jadwalKonseling?->status ?? ''));

        return in_array($statusSesi, ['selesai'], true)
            || in_array($statusJadwal, ['selesai'], true)
            || $this->isSessionExpiredForChat($sesi);
    }

    private function buildChatPayload(SesiKonseling $sesi, array $messages, bool $isReadyToStart): array
    {
        $jadwal = $sesi->jadwalKonseling;
        $konselorUser = optional(optional($jadwal)->konselor)->user;
        $konselorProfil = optional($konselorUser)->profil;
        $scheduledAt = $this->getScheduledAt($sesi);

        return [
            'sessionId' => $sesi->id,
            'jadwalId' => $jadwal?->id,
            'channel' => 'chat.sesi.'.$sesi->id,
            'startUrl' => route('mahasiswa.chat.start'),
            'sendUrl' => route('mahasiswa.chat.store'),
            'messagesUrl' => route('mahasiswa.chat.messages'),
            'updateUrlTemplate' => route('mahasiswa.chat.update', ['chat' => '__CHAT_ID__']),
            'deleteUrlTemplate' => route('mahasiswa.chat.destroy', ['chat' => '__CHAT_ID__']),
            'status' => $jadwal->status ?? 'disetujui',
            // Nama konselor chat mengikuti environment CIS agar konsisten dengan header dan pesan realtime.
            'counselorName' => env(
                'CIS_KONSELOR_NAME',
                $konselorUser?->getNamaDisplay()
                    ?? $konselorUser?->nama
                    ?? $konselorUser?->name
                    ?? 'Konselor'
            ),
            'counselorAvatar' => $konselorProfil?->foto ? Storage::url($konselorProfil->foto) : asset('img/default-avatar.png'),
            'canStart' => $isReadyToStart,
            'scheduledStartAt' => $scheduledAt?->toIso8601String(),
            'threadDateKey' => $this->resolveThreadDateKey($sesi),
            'threadDateLabel' => $this->resolveThreadDateLabel($sesi),
            'messages' => $messages,
            'chatSelesai' => $this->isReadOnlyChat($sesi),
            'readOnly' => $this->isReadOnlyChat($sesi),
        ];
    }

    private function transformMessage(Chat $chat, User $viewer): array
    {
        $chat->loadMissing([
            'pengirim.profil',
            'pengirim.mahasiswa',
            'replyTo.pengirim',
        ]);

        $sender = $chat->pengirim;
        $profil = optional($sender)->profil;
        $replyTo = $chat->replyTo;
        $replySender = $replyTo?->pengirim;

        return [
            'id' => $chat->id,
            'sesi_id' => $chat->sesi_id,
            'sender_id' => $chat->pengirim_id,
            'sender_name' => $sender?->getNamaDisplay() ?? 'Pengguna',
            'sender_role' => $sender?->role ?? 'pengguna',
            'avatar_url' => $profil?->foto ? Storage::url($profil->foto) : asset('img/default-avatar.png'),
            'text' => $chat->pesan,
            'reply_to' => $replyTo ? [
                'id' => $replyTo->id,
                'sender_id' => $replyTo->pengirim_id,
                'sender_name' => (int) $replyTo->pengirim_id === (int) $viewer->id
                    ? 'Anda'
                    : ($replySender?->getNamaDisplay() ?? 'Pengguna'),
                'text' => $replyTo->pesan,
            ] : null,
            'time' => $this->toDisplayDateTime($chat->created_at)?->format('H:i') ?? $this->nowInDisplayTimezone()->format('H:i'),
            'sent_at' => $this->toDisplayDateTime($chat->created_at)?->toIso8601String() ?? $this->nowInDisplayTimezone()->toIso8601String(),
            'updated_at' => $this->toDisplayDateTime($chat->updated_at)?->toIso8601String(),
            'is_edited' => (bool) ($chat->updated_at && $chat->created_at && $chat->updated_at->ne($chat->created_at)),
            'is_mine' => $chat->pengirim_id === $viewer->id,
        ];
    }

    private function resolveConversationMessages(SesiKonseling $activeSession, User $viewer): Collection
    {
        $activeSession->loadMissing([
            'jadwalKonseling.konselor',
            'jadwalKonseling.mahasiswa',
        ]);

        $jadwal = $activeSession->jadwalKonseling;

        if (! $jadwal) {
            return collect();
        }

        $sessionIds = SesiKonseling::query()
            ->whereHas('jadwalKonseling', function ($query) use ($jadwal) {
                $query->where('mahasiswa_id', $jadwal->mahasiswa_id)
                    ->where('konselor_id', $jadwal->konselor_id)
                    ->where('jenis', 'online')
                    ->whereIn('status', ['disetujui', 'berlangsung', 'selesai']);
            })
            ->pluck('id')
            ->push($activeSession->id)
            ->filter()
            ->unique()
            ->values();

        return Chat::query()
            ->with([
                'pengirim.profil',
                'pengirim.mahasiswa',
                'replyTo.pengirim',
            ])
            ->whereIn('sesi_id', $sessionIds)
            ->orderBy('created_at')
            ->get()
            ->map(fn (Chat $chat) => $this->transformMessage($chat, $viewer));
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
            'sesi.jadwalKonseling.konselor.user.profil',
            'sesi.jadwalKonseling.mahasiswa.user.profil',
        ]);

        $sesi = $chat->sesi;

        if (! $sesi) {
            return null;
        }

        if ((int) optional(optional($sesi->jadwalKonseling)->mahasiswa)->user_id !== (int) $user->id) {
            return null;
        }

        return $this->synchronizeSessionState($sesi);
    }
}
