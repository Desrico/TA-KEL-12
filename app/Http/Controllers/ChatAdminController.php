<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Concerns\HandlesOnlineChatSessions;
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
    use HandlesOnlineChatSessions;

    public function index(Request $request)
    {
        $user = $request->user();
        $jadwalList = $this->resolveAvailableSchedules($user);
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

        $messages = $this->resolveConversationMessages($sesi, $user)->all();

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
        $sesi = $this->resolveSessionFromRequest($request->user(), $request);

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

        return response()->json([
            'success' => true,
            'messages' => $this->resolveConversationMessages($sesi, $request->user())->all(),
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
            ->where('jenis', 'online')
            ->whereIn('status', ['disetujui', 'berlangsung', 'selesai'])
            ->get()
        )
            ->groupBy('mahasiswa_id')
            ->map(function (Collection $schedules) {
                return $schedules
                    ->reject(fn (JadwalKonseling $jadwal) => $jadwal->status === 'selesai')
                    ->sort(fn (JadwalKonseling $left, JadwalKonseling $right) => $left->compareSessionPriority($right))
                    ->first();
            })
            ->filter()
            ->sort(fn (JadwalKonseling $left, JadwalKonseling $right) => $left->compareSessionPriority($right))
            ->values();
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
                $byStudent = $jadwalList->firstWhere('mahasiswa_id', $requested->mahasiswa_id);

                if ($byStudent) {
                    return $byStudent;
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
        $jadwalId = $request->integer('jadwal_id');

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

        return $this->synchronizeSessionState($sesi);
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
            'updateUrlTemplate' => route('admin.chat.update', ['chat' => '__CHAT_ID__']),
            'deleteUrlTemplate' => route('admin.chat.destroy', ['chat' => '__CHAT_ID__']),
            'videoCallUrl' => $this->buildVideoCallUrl($sesi),
            'status' => $jadwal->status ?? 'disetujui',
            'studentName' => $mahasiswaUser?->getNamaDisplay() ?? 'Mahasiswa',
            'studentAvatar' => $mahasiswaProfil?->foto ? Storage::url($mahasiswaProfil->foto) : asset('img/default-avatar.png'),
            'canStart' => $isReadyToStart,
            'canStartNow' => $canStartNow,
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
            'updated_at' => $this->toDisplayDateTime($chat->updated_at)?->toIso8601String(),
            'is_edited' => (bool) ($chat->updated_at && $chat->created_at && $chat->updated_at->ne($chat->created_at)),
            'is_mine' => $chat->pengirim_id === $viewer->id,
        ];
    }

    private function buildVideoCallUrl(SesiKonseling $sesi): string
    {
        return 'https://meet.jit.si/campus-care-sesi-'.$sesi->id;
    }

    private function resolveConversationMessages(SesiKonseling $activeSession, User $viewer): Collection
    {
        $jadwal = $activeSession->jadwalKonseling;

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
}
