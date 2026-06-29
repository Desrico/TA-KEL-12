<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

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
    ];

    protected $table = 'group_chat_rooms';

    protected $fillable = [
        'topic',
        'title',
        'description',
        'visibility',
        'avatar_path',
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
        return self::TOPIC_OPTIONS + self::customPublicTopicOptions();
    }

    public static function defaultTopicOptions(): array
    {
        return self::TOPIC_OPTIONS;
    }

    public static function customPublicTopicOptions(): array
    {
        try {
            if (! Schema::hasTable('group_chat_rooms')) {
                return [];
            }

            return self::query()
                ->where('visibility', self::VISIBILITY_PUBLIC)
                ->where('is_active', true)
                ->whereNotIn('topic', array_keys(self::TOPIC_OPTIONS))
                ->orderBy('title')
                ->pluck('title', 'topic')
                ->filter(fn($title) => filled($title))
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public static function schedulingTopicOptions(): array
    {
        return [
            'Akademik (TA, Kuliah, KP, MBKM, others)' => 'Akademik (TA, Kuliah, KP, MBKM, others)',
            'Kehidupan di Kampus' => 'Kehidupan di Kampus',
            'Intrapersonal (Kecemasan, Kejenuhan, Motivasi Belajar, dll)' => 'Intrapersonal (Kecemasan, Kejenuhan, Motivasi Belajar, dll)',
            'Keluarga' => 'Keluarga',
            'Masalah di asrama' => 'Masalah di asrama',
            'Relasi (pertemanan, pacaran, ketidaknyamanan di asrama, kesalahpahaman)' => 'Relasi (pertemanan, pacaran, ketidaknyamanan di asrama, kesalahpahaman)',
        ] + collect(self::customPublicTopicOptions())
            ->mapWithKeys(fn($title) => [$title => $title])
            ->all();
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

        return self::TOPIC_OPTIONS[$this->topic] ?? ($this->title ?: ucfirst(str_replace('_', ' ', (string) $this->topic)));
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
