<?php

namespace App\Events;

use App\Models\SesiKonseling;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SesiSelesai implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SesiKonseling $sesi) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.sesi.' . $this->sesi->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'sesi.selesai';
    }

    public function broadcastWith(): array
    {
        return ['sesi_id' => $this->sesi->id];
    }
}