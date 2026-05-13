<?php

namespace App\Models;

use Carbon\Carbon;
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

    protected $casts = [
        'tanggal' => 'date',
    ];

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
        return $this->hasOne(SesiKonseling::class, SesiKonseling::jadwalForeignKey());
    }

    public function scheduledAt(?string $timezone = 'Asia/Jakarta'): ?Carbon
    {
        if (! $this->tanggal || ! $this->waktu) {
            return null;
        }

        $date = $this->tanggal instanceof Carbon
            ? $this->tanggal->copy()->timezone($timezone)
            : Carbon::parse($this->tanggal, $timezone);

        $time = $this->waktu instanceof Carbon
            ? $this->waktu->format('H:i:s')
            : trim((string) $this->waktu);

        if ($time === '') {
            return null;
        }

        return $date
            ->startOfDay()
            ->setTimeFromTimeString($time);
    }

    public function hasScheduledTimeStarted(?Carbon $reference = null, string $timezone = 'Asia/Jakarta'): bool
    {
        $scheduledAt = $this->scheduledAt($timezone);

        if (! $scheduledAt) {
            return false;
        }

        $referenceTime = $reference?->copy()->timezone($timezone) ?? Carbon::now($timezone);

        return $referenceTime->greaterThanOrEqualTo($scheduledAt);
    }

    public function scheduledEndAt(?string $timezone = 'Asia/Jakarta'): ?Carbon
    {
        $scheduledAt = $this->scheduledAt($timezone);

        if (! $scheduledAt) {
            return null;
        }

        return $scheduledAt->copy()->addDay();
    }

    public function hasChatWindowEnded(?Carbon $reference = null, string $timezone = 'Asia/Jakarta'): bool
    {
        $scheduledEndAt = $this->scheduledEndAt($timezone);

        if (! $scheduledEndAt) {
            return false;
        }

        $referenceTime = $reference?->copy()->timezone($timezone) ?? Carbon::now($timezone);

        return $referenceTime->greaterThanOrEqualTo($scheduledEndAt);
    }

    public function isChatWindowOpen(?Carbon $reference = null, string $timezone = 'Asia/Jakarta'): bool
    {
        return $this->hasScheduledTimeStarted($reference, $timezone)
            && ! $this->hasChatWindowEnded($reference, $timezone);
    }

    public function compareSessionPriority(self $other, string $timezone = 'Asia/Jakarta'): int
    {
        $thisScheduledAt = $this->scheduledAt($timezone);
        $otherScheduledAt = $other->scheduledAt($timezone);

        $thisRank = $this->sessionPriorityRank($timezone);
        $otherRank = $other->sessionPriorityRank($timezone);

        if ($thisRank !== $otherRank) {
            return $thisRank <=> $otherRank;
        }

        if (! $thisScheduledAt && ! $otherScheduledAt) {
            return $this->id <=> $other->id;
        }

        if (! $thisScheduledAt) {
            return 1;
        }

        if (! $otherScheduledAt) {
            return -1;
        }

        if ($thisRank <= 1) {
            return $otherScheduledAt <=> $thisScheduledAt;
        }

        return $thisScheduledAt <=> $otherScheduledAt;
    }

    private function sessionPriorityRank(string $timezone = 'Asia/Jakarta'): int
    {
        if ($this->isChatWindowOpen(null, $timezone) && $this->status === 'berlangsung') {
            return 0;
        }

        if ($this->isChatWindowOpen(null, $timezone)) {
            return 1;
        }

        if ($this->scheduledAt($timezone) && ! $this->hasChatWindowEnded(null, $timezone)) {
            return 2;
        }

        if ($this->scheduledAt($timezone)) {
            return 3;
        }

        return 4;
    }
}
