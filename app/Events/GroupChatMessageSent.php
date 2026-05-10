<?php

namespace App\Events;

use App\Models\GroupChatMessage;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GroupChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public GroupChatMessage $message)
    {
        $this->message->loadMissing([
            'sender.profil',
            'sender.mahasiswa',
            'room',
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.group.'.$this->message->room_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'group.chat.message.sent';
    }

    public function broadcastWith(): array
    {
        $sender = $this->message->sender;
        $profil = optional($sender)->profil;
        $createdAt = $this->toDisplayDateTime($this->message->created_at);

        return [
            'message' => [
                'id' => $this->message->id,
                'room_id' => $this->message->room_id,
                'sender_id' => $this->message->user_id,
                'sender_name' => $sender?->getNamaDisplay() ?? 'Pengguna',
                'sender_role' => $sender?->role ?? 'pengguna',
                'avatar_url' => $profil?->foto ? Storage::url($profil->foto) : asset('img/default-avatar.png'),
                'text' => $this->message->pesan,
                'time' => $createdAt?->format('H:i') ?? Carbon::now($this->displayTimezone())->format('H:i'),
                'sent_at' => $createdAt?->toIso8601String() ?? Carbon::now($this->displayTimezone())->toIso8601String(),
                'updated_at' => $this->toDisplayDateTime($this->message->updated_at)?->toIso8601String(),
                'is_edited' => (bool) ($this->message->updated_at && $this->message->created_at && $this->message->updated_at->ne($this->message->created_at)),
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
