<?php

/**
 * RUTE KONSOL & JADWAL — SIG-Udeung
 *
 * Perintah Artisan kustom dan jadwal task terjadwal.
 *
 * Jadwal:
 * - system:cleanup → harian pukul 02:00 (pembersihan data sementara)
 *
 * @see \App\Console\Commands\SystemCleanupCommand
 */

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('system:cleanup')->daily()->at('02:00');

Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=55')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
