<?php

namespace App\Events;

use App\Models\GroupChatMessage;
use App\Support\GroupChatSupport;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
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
        $createdAt = $this->toDisplayDateTime($this->message->created_at);
        $room = $this->message->room;
        $membership = ($sender && $room) ? GroupChatSupport::resolveRoomMember($sender, $room) : null;
        $isSystemMessage = (bool) $this->message->is_system;
        $senderRole = strtolower((string) ($sender?->role ?? 'pengguna'));
        $isCounselorMessage = ! $isSystemMessage && in_array($senderRole, ['konselor', 'admin'], true);

        return [
            'message' => [
                'id' => $this->message->id,
                'room_id' => $this->message->room_id,
                'sender_id' => $this->message->user_id,
                'sender_name' => $isSystemMessage
                    ? 'Sistem'
                    : ($isCounselorMessage ? 'Konselor' : GroupChatSupport::resolveDisplayName($sender, $room, $membership)),
                'sender_role' => $sender?->role ?? 'pengguna',
                'avatar_url' => GroupChatSupport::resolveAvatarUrl(),
                'avatar_initial' => $isCounselorMessage ? 'K' : null,
                'is_counselor' => $isCounselorMessage,
                'text' => $this->message->pesan,
                'time' => $createdAt?->format('H:i') ?? Carbon::now($this->displayTimezone())->format('H:i'),
                'sent_at' => $createdAt?->toIso8601String() ?? Carbon::now($this->displayTimezone())->toIso8601String(),
                'updated_at' => $this->toDisplayDateTime($this->message->updated_at)?->toIso8601String(),
                'is_edited' => (bool) ($this->message->updated_at && $this->message->created_at && $this->message->updated_at->ne($this->message->created_at)),
                'is_system' => $isSystemMessage,
                'system_event' => $this->message->system_event,
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
