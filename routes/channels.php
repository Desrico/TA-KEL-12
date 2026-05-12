<?php

use App\Models\GroupChatRoom;
use App\Models\SesiKonseling;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.sesi.{sesiId}', function ($user, $sesiId) {
    $sesi = SesiKonseling::query()
        ->with('jadwalKonseling')
        ->find($sesiId);

    if (! $sesi || ! $sesi->jadwalKonseling) {
        return false;
    }

    $jadwal = $sesi->jadwalKonseling;

    if ($user->role === 'mahasiswa') {
        return (int) optional($user->mahasiswa)->id === (int) $jadwal->mahasiswa_id;
    }

    if ($user->role === 'konselor') {
        return (int) optional($user->konselor)->id === (int) $jadwal->konselor_id;
    }

    return false;
});

Broadcast::channel('chat.group.{roomId}', function ($user, $roomId) {
    if ($user->role === 'konselor') {
        return true;
    }

    if ($user->role !== 'mahasiswa') {
        return false;
    }

    return GroupChatRoom::query()
        ->whereKey($roomId)
        ->whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->exists();
});
