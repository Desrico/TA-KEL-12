<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JadwalKonseling extends Model
{
    public const SESSION_TIMEZONE = 'Asia/Jakarta';

    protected $table = 'jadwal_konseling';

    protected $fillable = [
        'mahasiswa_id',
        'konselor_id',
        'tanggal',
        'waktu',
        'status',
        'started_at',
        'expires_at',
        'jenis',
        'topik',
        'anonim',
        'catatan',
        'ringkasan_masalah',
        'observasi_konselor',
        'progress',
        'tindak_lanjut',
        'tindak_lanjut_tipe',
        'tanggal_lanjut',
        'laporan',
        'alasan_penolakan',
    ];

    public static function sessionTimezone(): string
    {
        return self::SESSION_TIMEZONE;
    }

    public static function sessionNow(): Carbon
    {
        return Carbon::now(self::sessionTimezone());
    }

    public function isOnlineType(): bool
    {
        return strtolower(trim((string) $this->jenis)) === 'online';
    }

    public function scheduledAt(): ?Carbon
    {
        if (! $this->tanggal || ! $this->waktu) {
            return null;
        }

        return Carbon::parse(
            trim((string) $this->tanggal.' '.(string) $this->waktu),
            self::sessionTimezone()
        );
    }

    public function scheduledStartLabel(): string
    {
        $scheduledAt = $this->scheduledAt();

        if (! $scheduledAt) {
            return 'jadwal yang ditentukan';
        }

        return $scheduledAt->translatedFormat('j F Y \\p\\u\\k\\u\\l H:i');
    }

    public function hasScheduledTimeStarted(?CarbonInterface $reference = null): bool
    {
        $scheduledAt = $this->scheduledAt();

        if (! $scheduledAt) {
            return false;
        }

        $referenceTime = $reference
            ? Carbon::instance($reference)->timezone(self::sessionTimezone())
            : self::sessionNow();

        return $referenceTime->greaterThanOrEqualTo($scheduledAt);
    }

    public function sessionPriorityRank(): int
    {
        if ($this->status === 'berlangsung') {
            return 0;
        }

        return $this->hasScheduledTimeStarted() ? 1 : 2;
    }

    public function sessionPriorityTimestamp(): int
    {
        return $this->scheduledAt()?->getTimestamp() ?? PHP_INT_MAX;
    }

    public function compareSessionPriority(self $other): int
    {
        $rankComparison = $this->sessionPriorityRank() <=> $other->sessionPriorityRank();

        if ($rankComparison !== 0) {
            return $rankComparison;
        }

        $timeComparison = $this->sessionPriorityTimestamp() <=> $other->sessionPriorityTimestamp();

        if ($timeComparison !== 0) {
            return $timeComparison;
        }

        return $this->id <=> $other->id;
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function konselor(): BelongsTo
    {
        return $this->belongsTo(Konselor::class, 'konselor_id');
    }

    public function sesiKonseling(): HasOne
    {
        return $this->hasOne(SesiKonseling::class, 'jadwal_id');
    }

    public function startedAt(): ?\Carbon\Carbon
    {
        return $this->started_at ? Carbon::parse($this->started_at, self::sessionTimezone()) : null;
    }

    public function expiresAt(): ?\Carbon\Carbon
    {
        if ($this->expires_at) {
            return Carbon::parse($this->expires_at, self::sessionTimezone());
        }

        $start = $this->startedAt() ?? $this->scheduledAt();

        return $start ? $start->copy()->addDay() : null;
    }

    public function isExpired(?\Carbon\CarbonInterface $reference = null): bool
    {
        $now = $reference ? Carbon::instance($reference)->timezone(self::sessionTimezone()) : self::sessionNow();
        $expires = $this->expiresAt();

        if (! $expires) {
            return false;
        }

        return $now->greaterThan($expires);
    }

    public function syncExpiredSessionStatus(?CarbonInterface $reference = null): bool
    {
        if (! in_array($this->status, ['disetujui', 'berlangsung'], true)) {
            return false;
        }

        if (! $this->isExpired($reference)) {
            return false;
        }

        $this->forceFill([
            'status' => 'selesai',
        ])->save();

        $sesi = $this->sesiKonseling;

        if ($sesi && $sesi->status !== 'selesai') {
            $sesi->forceFill([
                'status' => 'selesai',
            ])->save();
        }

        return true;
    }
}
