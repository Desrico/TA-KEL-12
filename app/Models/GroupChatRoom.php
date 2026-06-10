<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GroupChatRoom extends Model
{
    use HasFactory;

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PRIVATE = 'private';

    public const TOPIC_OPTIONS = [
        'akademik' => 'Akademik',
        'kehidupan_kampus' => 'Kehidupan di Kampus',
        'intrapersonal' => 'Intrapersonal',
        'keluarga' => 'Keluarga',
        'masalah_asrama' => 'Masalah di Asrama',
        'relasi' => 'Relasi',
        'lainnya' => 'Lainnya',
    ];

    protected $table = 'group_chat_rooms';

    protected $fillable = [
        'topic',
        'title',
        'description',
        'visibility',
        'invite_token',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'visibility' => 'string',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function topicOptions(): array
    {
        return self::TOPIC_OPTIONS;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupChatMember::class, 'room_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(GroupChatMessage::class, 'room_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(GroupChatMessage::class, 'room_id')->latestOfMany();
    }

    public function topicLabel(): string
    {
        if ($this->isPrivate()) {
            return 'Grup Privat';
        }

        return self::TOPIC_OPTIONS[$this->topic] ?? ucfirst(str_replace('_', ' ', (string) $this->topic));
    }

    public function isPublic(): bool
    {
        return ($this->visibility ?? self::VISIBILITY_PUBLIC) === self::VISIBILITY_PUBLIC;
    }

    public function isPrivate(): bool
    {
        return ($this->visibility ?? self::VISIBILITY_PUBLIC) === self::VISIBILITY_PRIVATE;
    }

    public function visibilityLabel(): string
    {
        return $this->isPrivate() ? 'Privat' : 'Publik';
    }
}
