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
        $sesi = $this->resolveActiveSession($user);

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
        $sesi = $this->resolveActiveSession($request->user());

        if (! $sesi) {
            return redirect()
                ->route('mahasiswa.chat')
                ->with('error', 'Belum ada sesi konseling online yang bisa dimulai.');
        }

        $jadwal = $sesi->jadwalKonseling;

        if (! $this->canStartSessionNow($sesi)) {
            return redirect()
                ->route('mahasiswa.chat')
                ->with('error', $this->getScheduleBlockedMessage($sesi));
        }

        if ($this->isSessionActive($sesi)) {
            return redirect()->route('mahasiswa.chat');
        }

        if (($jadwal->status ?? null) !== 'disetujui') {
            return redirect()
                ->route('mahasiswa.chat')
                ->with('error', 'Sesi belum siap dimulai.');
        }

        $this->activateSessionIfNeeded($sesi);

        return redirect()
            ->route('mahasiswa.chat')
            ->with('success', 'Sesi konseling berhasil dimulai.');
    }

    public function messages(Request $request): JsonResponse
    {
        $user = $request->user();
        $sesi = $this->resolveActiveSession($user);

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
            'pesan' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $sesi = $this->resolveActiveSession($user);

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

    private function resolveActiveSession(?User $user): ?SesiKonseling
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

        $jadwal = JadwalKonseling::query()
            ->with([
                'konselor.user.profil',
                'mahasiswa.user.profil',
            ])
            ->where('mahasiswa_id', $mahasiswaId)
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
            ->first();

        if (! $jadwal) {
            return null;
        }

        $sesi = SesiKonseling::firstOrCreate(
            ['jadwal_id' => $jadwal->id],
            [
                'status' => $jadwal->status === 'berlangsung' ? 'berlangsung' : 'disetujui',
            ]
        );

        if (in_array($sesi->status, ['selesai', 'dibatalkan'], true)) {
            return null;
        }

        $sesi->setRelation('jadwalKonseling', $jadwal);

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
        return $sesi->status === 'berlangsung';
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
