<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
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

class ChatMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $sesi = $this->resolveActiveSession($user, $request->integer('jadwal'));

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
            'chats.pengirim.profil',
            'chats.pengirim.mahasiswa',
        ]);

        if (
            $request->filled('jadwal')
            && $this->canStartSessionNow($sesi)
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
        $messages = $sesi->chats
            ->sortBy('created_at')
            ->values()
            ->map(fn (Chat $chat) => $this->transformMessage($chat, $user))
            ->all();

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

        return redirect()
            ->route('mahasiswa.chat', ['jadwal' => $jadwal?->id])
            ->with('success', 'Sesi konseling berhasil dimulai.');
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
                ->map(fn (Chat $chat) => $this->transformMessage($chat, $user))
                ->all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sesi_id' => 'nullable|integer',
            'jadwal_id' => 'nullable|integer',
            'pesan' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $sesi = $this->resolveSessionFromRequest($user, $request);

        if (! $sesi) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada sesi konseling online aktif.',
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

        $chat = DB::transaction(function () use ($validated, $sesi, $user) {
            return Chat::create([
                'sesi_id' => $sesi->id,
                'pengirim_id' => $user->id,
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

        $jadwal = $jadwalId
            ? $this->resolveScheduleForStudent($user, $jadwalId)
            : JadwalKonseling::query()
                ->with([
                    'konselor.user.profil',
                    'mahasiswa.user.profil',
                ])
                ->where('mahasiswa_id', $mahasiswaId)
                ->where('jenis', 'online')
                ->whereIn('status', ['disetujui', 'berlangsung'])
                ->get()
                ->sort(fn (JadwalKonseling $left, JadwalKonseling $right) => $left->compareSessionPriority($right))
                ->first();

        if (! $jadwal) {
            return null;
        }

        $sesi = $this->resolveSessionFromSchedule($jadwal);

        if (in_array($sesi->status, ['selesai', 'dibatalkan'], true)) {
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
            ->whereIn('status', ['disetujui', 'berlangsung'])
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
                'chats.pengirim.profil',
                'chats.pengirim.mahasiswa',
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

    private function resolveSessionFromSchedule(JadwalKonseling $jadwal): SesiKonseling
    {
        $sesi = SesiKonseling::firstOrCreate(
            [SesiKonseling::jadwalForeignKey() => $jadwal->id],
            [
                'status' => $jadwal->status === 'berlangsung' ? 'berlangsung' : 'disetujui',
            ]
        );

        $sesi->setRelation('jadwalKonseling', $jadwal);

        return $this->synchronizeSessionState($sesi);
    }

    private function synchronizeSessionState(SesiKonseling $sesi): SesiKonseling
    {
        $jadwal = $sesi->jadwalKonseling;

        if (! $jadwal) {
            $jadwal = $sesi->jadwalKonseling()->with([
                'konselor.user.profil',
                'mahasiswa.user.profil',
            ])->first();

            if ($jadwal) {
                $sesi->setRelation('jadwalKonseling', $jadwal);
            }
        }

        if (! $jadwal) {
            return $sesi;
        }

        $updates = [];

        if (($jadwal->status ?? null) === 'berlangsung' && $sesi->status !== 'berlangsung') {
            $updates['status'] = 'berlangsung';
        }

        if ($updates) {
            $sesi->forceFill($updates)->save();
            $sesi->refresh();
            $sesi->setRelation('jadwalKonseling', $jadwal);
        }

        if ($sesi->status === 'berlangsung' && $jadwal->status !== 'berlangsung') {
            $jadwal->forceFill(['status' => 'berlangsung'])->save();
            $jadwal->refresh();
            $sesi->setRelation('jadwalKonseling', $jadwal);
        }

        return $sesi;
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

    private function buildChatPayload(SesiKonseling $sesi, array $messages, bool $isReadyToStart): array
    {
        $jadwal = $sesi->jadwalKonseling;
        $konselorUser = optional(optional($jadwal)->konselor)->user;
        $konselorProfil = optional($konselorUser)->profil;

        return [
            'sessionId' => $sesi->id,
            'jadwalId' => $jadwal?->id,
            'channel' => 'chat.sesi.'.$sesi->id,
            'startUrl' => route('mahasiswa.chat.start'),
            'sendUrl' => route('mahasiswa.chat.store'),
            'messagesUrl' => route('mahasiswa.chat.messages'),
            'status' => $jadwal->status ?? 'disetujui',
            'counselorName' => $konselorUser?->nama ?? 'Konselor Kampus Care',
            'counselorAvatar' => $konselorProfil?->foto ? Storage::url($konselorProfil->foto) : asset('img/default-avatar.png'),
            'canStart' => $isReadyToStart,
            'messages' => $messages,
        ];
    }

    private function canStartSessionNow(SesiKonseling $sesi): bool
    {
        $scheduledAt = $this->getScheduledAt($sesi);

        if (! $scheduledAt) {
            return false;
        }

        return $this->nowInDisplayTimezone()->greaterThanOrEqualTo($scheduledAt);
    }

    private function isSessionActive(SesiKonseling $sesi): bool
    {
        return $sesi->status === 'berlangsung'
            || ($sesi->jadwalKonseling?->status === 'berlangsung');
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

    private function getScheduleBlockedMessage(SesiKonseling $sesi): string
    {
        return 'Sesi konseling online ini akan dimulai pada '.$this->getScheduledStartLabel($sesi).'. Sebelum itu, ruang chat belum bisa diakses.';
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

    private function getScheduledAt(SesiKonseling $sesi): ?Carbon
    {
        return $sesi->jadwalKonseling?->scheduledAt();
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
