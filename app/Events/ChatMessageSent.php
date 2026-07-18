<?php

namespace App\Events;

use App\Models\Chat;
use App\Models\JadwalKonseling;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Chat $chat)
    {
        $this->chat->loadMissing([
            'pengirim.profil',
            'pengirim.mahasiswa',
            'replyTo.pengirim',
            'sesi.jadwalKonseling',
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.sesi.'.$this->chat->sesi_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message.sent';
    }

    public function broadcastWith(): array
    {
        $sender = $this->chat->pengirim;
        $profil = optional($sender)->profil;
        $jadwal = $this->chat->sesi?->jadwalKonseling;
        $replyTo = $this->chat->replyTo;
        $replySender = $replyTo?->pengirim;
        $createdAt = $this->toDisplayDateTime($this->chat->created_at);
        $isAnonymousStudent = $this->usesAnonymousScheduleIdentity($sender, $jadwal);

        return [
            'message' => [
                'id' => $this->chat->id,
                'sesi_id' => $this->chat->sesi_id,
                'sender_id' => $this->chat->pengirim_id,
                'sender_name' => $this->resolveScheduleSenderName($sender, $jadwal),
                'sender_role' => $sender?->role ?? 'pengguna',
                'avatar_url' => ! $isAnonymousStudent && $profil?->foto
                    ? Storage::url($profil->foto)
                    : asset('img/default-avatar.png'),
                'text' => $this->chat->pesan,
                'reply_to' => $replyTo ? [
                    'id' => $replyTo->id,
                    'sender_id' => $replyTo->pengirim_id,
                    'sender_name' => $this->resolveScheduleSenderName($replySender, $jadwal),
                    'text' => $replyTo->pesan,
                ] : null,
                'time' => $createdAt?->format('H:i') ?? Carbon::now($this->displayTimezone())->format('H:i'),
                'sent_at' => $createdAt?->toIso8601String() ?? Carbon::now($this->displayTimezone())->toIso8601String(),
                'updated_at' => $this->toDisplayDateTime($this->chat->updated_at)?->toIso8601String(),
                'is_edited' => (bool) ($this->chat->updated_at && $this->chat->created_at && $this->chat->updated_at->ne($this->chat->created_at)),
            ],
        ];
    }

    private function usesAnonymousScheduleIdentity(?User $sender, ?JadwalKonseling $jadwal): bool
    {
        return ($sender?->role ?? null) === 'mahasiswa'
            && (bool) ($jadwal?->anonim ?? false);
    }

    private function resolveScheduleSenderName(?User $sender, ?JadwalKonseling $jadwal): string
    {
        if (! $sender) {
            return 'Pengguna';
        }

        if ($this->usesAnonymousScheduleIdentity($sender, $jadwal)) {
            return trim($sender->getAnonimDisplayName()) ?: 'Anonim';
        }

        return $sender->nama ?? 'Pengguna';
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

    private function displayTimezone(): string
    {
        return 'Asia/Jakarta';
    }
}
