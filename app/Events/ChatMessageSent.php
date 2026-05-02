<?php

namespace App\Events;

use App\Models\Chat;
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
        $createdAt = $this->toDisplayDateTime($this->chat->created_at);

        return [
            'message' => [
                'id' => $this->chat->id,
                'sesi_id' => $this->chat->sesi_id,
                'sender_id' => $this->chat->pengirim_id,
                'sender_name' => $sender?->getNamaDisplay() ?? 'Pengguna',
                'sender_role' => $sender?->role ?? 'pengguna',
                'avatar_url' => $profil?->foto ? Storage::url($profil->foto) : asset('img/default-avatar.png'),
                'text' => $this->chat->pesan,
                'time' => $createdAt?->format('H:i') ?? Carbon::now($this->displayTimezone())->format('H:i'),
                'sent_at' => $createdAt?->toIso8601String() ?? Carbon::now($this->displayTimezone())->toIso8601String(),
            ],
        ];
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
