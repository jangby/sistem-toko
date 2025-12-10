<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Jadwalkan perintah 'app:daily-report' setiap hari jam 21:00
Schedule::command('app:daily-report')
        ->dailyAt('21:00')
        ->timezone('Asia/Jakarta');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
