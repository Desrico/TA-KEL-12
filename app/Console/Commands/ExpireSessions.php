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

        $expired = JadwalKonseling::query()
            ->whereNotNull('expires_at')
            ->where('status', 'berlangsung')
            ->where('expires_at', '<=', $now)
            ->get();

        $count = 0;

        foreach ($expired as $jadwal) {
            $jadwal->status = 'selesai';
            $jadwal->save();
            $count++;
        }

        $this->info("Expired sessions processed: {$count}");

        return 0;
    }
}
