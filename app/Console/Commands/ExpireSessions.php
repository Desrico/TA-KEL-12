<?php

namespace App\Console\Commands;

use App\Models\JadwalKonseling;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireSessions extends Command
{
    protected $signature = 'sessions:expire';

    protected $description = 'Mark sesi_konseling / jadwal_konseling as finished when expires_at has passed.';

    public function handle()
    {
        $now = Carbon::now('Asia/Jakarta');

        // Tetap cek semua sesi yang masih hidup agar data lama tanpa expires_at ikut tersinkron.
        $expired = JadwalKonseling::query()
            ->whereIn('status', ['disetujui', 'berlangsung'])
            ->get();

        $count = 0;

        foreach ($expired as $jadwal) {
            if ($jadwal->syncExpiredSessionStatus($now)) {
                $count++;
            }
        }

        $this->info("Expired sessions processed: {$count}");

        return 0;
    }
}
