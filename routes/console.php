<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands - Bureau Activity Tracking
|--------------------------------------------------------------------------
*/

// Check for overdue activities every hour
Schedule::command('activities:check-overdue')->hourly();

// Escalate overdue activities daily at 8 AM
Schedule::command('activities:escalate')->dailyAt('08:00');
