<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*  Check email
    php artisan app:send-maintenance-reminders
*/
// Schedule::command('app:send-maintenance-reminders')->everyMinute();
Schedule::command('app:send-maintenance-reminders')->dailyAt('08:00');

// exp SIM, kir, kendaraan
Schedule::command('app:send-expiry-reminders')->dailyAt('08:30');
// Schedule::command('app:send-expiry-reminders')->everyMinute();