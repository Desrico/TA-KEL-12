<?php

namespace App\Http\Controllers\Concerns;

use App\Models\JadwalKonseling;
use App\Models\SesiKonseling;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

trait HandlesOnlineChatSessions
{
    protected function resolveSessionFromSchedule(JadwalKonseling $jadwal): SesiKonseling
    {
        $foreignKey = SesiKonseling::jadwalForeignKey();
        $attributes = [$foreignKey => $jadwal->id];
        $defaults = [
            'status' => ($jadwal->status === 'berlangsung' && ! $jadwal->hasChatWindowEnded()) ? 'berlangsung' : 'disetujui',
        ];

        if (! Schema::hasColumn('sesi_konseling', 'status')) {
            unset($defaults['status']);
        }

        $sesi = SesiKonseling::query()->firstOrCreate($attributes, $defaults);
        $sesi->setRelation('jadwalKonseling', $jadwal);

        return $this->synchronizeSessionState($sesi);
    }

    protected function synchronizeSessionState(SesiKonseling $sesi): SesiKonseling
    {
        $jadwal = $sesi->jadwalKonseling;

        if (! $jadwal) {
            $jadwal = $sesi->jadwalKonseling()->with([
                'konselor.user.profil',
                'mahasiswa.user.profil',
            ])->first();

            if ($jadwal) {
                $sesi->setRelation('jadwalKonseling', $jadwal);
            }
        }

        if (! $jadwal) {
            return $sesi;
        }

        $updates = [];
        $expired = $jadwal->hasChatWindowEnded(null, $this->displayTimezone());
        $windowOpen = $jadwal->isChatWindowOpen(null, $this->displayTimezone());
        $sessionHasStatus = Schema::hasColumn('sesi_konseling', 'status');

        if ($expired) {
            if ($sessionHasStatus && $sesi->status !== 'selesai') {
                $updates['status'] = 'selesai';
            }

            if (Schema::hasColumn('sesi_konseling', 'waktu_selesai') && empty($sesi->waktu_selesai)) {
                $updates['waktu_selesai'] = now();
            }

            if (in_array($jadwal->status, ['disetujui', 'berlangsung'], true)) {
                $jadwal->forceFill(['status' => 'selesai'])->save();
                $jadwal->refresh();
                $sesi->setRelation('jadwalKonseling', $jadwal);
            }
        } elseif (($jadwal->status ?? null) === 'berlangsung' && $sessionHasStatus && $sesi->status !== 'berlangsung') {
            $updates['status'] = 'berlangsung';
        }

        if ($updates) {
            $sesi->forceFill($updates)->save();
            $sesi->refresh();
            $sesi->setRelation('jadwalKonseling', $jadwal);
        }

        if (
            $windowOpen
            && $sessionHasStatus
            && $sesi->status === 'berlangsung'
            && $jadwal->status !== 'berlangsung'
        ) {
            $jadwal->forceFill(['status' => 'berlangsung'])->save();
            $jadwal->refresh();
            $sesi->setRelation('jadwalKonseling', $jadwal);
        }

        return $sesi;
    }

    protected function activateSessionIfNeeded(SesiKonseling $sesi): void
    {
        $sesi = $this->synchronizeSessionState($sesi);

        if (! $this->canStartSessionNow($sesi)) {
            return;
        }

        $updates = [];

        if (Schema::hasColumn('sesi_konseling', 'status') && $sesi->status !== 'berlangsung') {
            $updates['status'] = 'berlangsung';
        }

        if (Schema::hasColumn('sesi_konseling', 'waktu_mulai') && empty($sesi->waktu_mulai)) {
            $updates['waktu_mulai'] = now();
        }

        if ($updates) {
            $sesi->forceFill($updates)->save();
            $sesi->refresh();
        }

        $jadwal = $sesi->jadwalKonseling;

        if ($jadwal && $jadwal->status !== 'berlangsung') {
            $jadwal->forceFill(['status' => 'berlangsung'])->save();
        }
    }

    protected function canStartSessionNow(SesiKonseling $sesi): bool
    {
        $sesi = $this->synchronizeSessionState($sesi);
        $jadwal = $sesi->jadwalKonseling;

        return (bool) $jadwal?->isChatWindowOpen(null, $this->displayTimezone());
    }

    protected function isSessionActive(SesiKonseling $sesi): bool
    {
        $sesi = $this->synchronizeSessionState($sesi);

        return $this->canStartSessionNow($sesi)
            && (
                $sesi->status === 'berlangsung'
                || ($sesi->jadwalKonseling?->status === 'berlangsung')
            );
    }

    protected function getScheduledAt(SesiKonseling $sesi): ?Carbon
    {
        return $sesi->jadwalKonseling?->scheduledAt($this->displayTimezone());
    }

    protected function getScheduledEndAt(SesiKonseling $sesi): ?Carbon
    {
        return $sesi->jadwalKonseling?->scheduledEndAt($this->displayTimezone());
    }

    protected function getScheduledStartLabel(SesiKonseling $sesi): string
    {
        $scheduledAt = $this->getScheduledAt($sesi);

        if (! $scheduledAt) {
            return 'jadwal yang ditentukan';
        }

        return $scheduledAt->translatedFormat('j F Y \\p\\u\\k\\u\\l H:i');
    }

    protected function getScheduleBlockedMessage(SesiKonseling $sesi): string
    {
        $scheduledAt = $this->getScheduledAt($sesi);
        $scheduledEndAt = $this->getScheduledEndAt($sesi);

        if (! $scheduledAt) {
            return 'Jadwal sesi belum lengkap sehingga ruang chat belum bisa diakses.';
        }

        if ($scheduledEndAt && $this->nowInDisplayTimezone()->greaterThanOrEqualTo($scheduledEndAt)) {
            return 'Sesi konseling online ini sudah melewati batas 24 jam dan dinyatakan selesai.';
        }

        return 'Sesi konseling online ini akan dimulai pada '.$this->getScheduledStartLabel($sesi).'. Sebelum itu, ruang chat belum bisa diakses.';
    }

    protected function toDisplayDateTime($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        return $value instanceof Carbon
            ? $value->copy()->timezone($this->displayTimezone())
            : Carbon::parse($value)->timezone($this->displayTimezone());
    }

    protected function nowInDisplayTimezone(): Carbon
    {
        return Carbon::now($this->displayTimezone());
    }

    protected function displayTimezone(): string
    {
        return 'Asia/Jakarta';
    }
}
