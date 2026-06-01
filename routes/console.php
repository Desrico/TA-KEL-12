<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\DashboardController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Jadwal untuk memindai kondisi mahasiswa (Level 0-3) secara keseluruhan tiap 1 jam
Schedule::call(function () {
    app(DashboardController::class)->scanLevel3();
})->hourly();
