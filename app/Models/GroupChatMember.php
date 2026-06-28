<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupChatMember extends Model
{
    use HasFactory;

    public const STATUS_INVITED = 'invited';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_REMOVED = 'removed';
    public const STATUS_LEFT = 'left'; // Status ketika user keluar grup

    protected $table = 'group_chat_members';

    protected $fillable = [
        'room_id',
        'user_id',
        'anonymous_name',
        'membership_status',    // Status keanggotaan
        'joined_at',
        'consented_at',
        'consent_version',
        'joined_via',
        'invited_by',
        'removed_at',       // Waktu keluar
        'removed_reason',   // Alasan keluar (left_by_member)
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'consented_at' => 'datetime',
        'removed_at' => 'datetime',
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

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isActive(): bool
    {
        return $this->membership_status === self::STATUS_ACTIVE;
    }

    public function isInvited(): bool
    {
        return $this->membership_status === self::STATUS_INVITED;
    }
}
