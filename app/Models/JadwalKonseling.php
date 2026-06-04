<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\SesiKonseling;

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
        return $this->hasOne(SesiKonseling::class, SesiKonseling::jadwalForeignKey(), 'id');
    }

    public static function sessionTimezone(): string
    {
        return self::SESSION_TIMEZONE;
    }

    public static function sessionNow(): Carbon
    {
        return Carbon::now(self::SESSION_TIMEZONE);
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

    public function startedAt(): ?\Carbon\Carbon
    {
        return $this->started_at ? Carbon::parse($this->started_at, self::sessionTimezone()) : null;
    }

    public function expiresAt(): ?\Carbon\Carbon
    {
        // Expiry tetap mengikuti jam booking + 24 jam agar jendela chat tidak ikut bergeser saat sesi dimulai lebih lambat.
        if ($this->expires_at) {
            return Carbon::parse($this->expires_at, self::sessionTimezone());
        }

        $start = $this->scheduledAt() ?? $this->startedAt();

        return $start ? $start->copy()->addDay() : null;
    }

    public function isExpired(?\Carbon\CarbonInterface $reference = null): bool
    {
        $now = $reference ? Carbon::instance($reference)->timezone(self::sessionTimezone()) : self::sessionNow();
        $expires = $this->expiresAt();

        if (! $expires) {
            return false;
        }

        // Tepat di batas 24 jam sesi harus dianggap selesai dan akses chat ditutup.
        return $now->greaterThanOrEqualTo($expires);
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

    public function scheduledStartLabel(?string $timezone = 'Asia/Jakarta'): ?string
    {
        $scheduledAt = $this->scheduledAt($timezone);

        if (! $scheduledAt) {
            return null;
        }

        return $scheduledAt->translatedFormat('j F Y') . ' pukul ' . $scheduledAt->format('H:i');
    }
}
