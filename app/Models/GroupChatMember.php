<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupChatMember extends Model
{
    use HasFactory;

    protected $table = 'group_chat_members';

    protected $fillable = [
        'room_id',
        'user_id',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(GroupChatRoom::class, 'room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
