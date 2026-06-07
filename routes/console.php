<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Deadline Reminder Scheduler ───────────────────────────────────────────────
// Runs every 15 minutes — dispatches reminder jobs for tasks due in 48/24/12/1h
Schedule::command('reminders:send')->everyFifteenMinutes();

// ── Overdue Task Scanner ───────────────────────────────────────────────────────
// Runs every hour — marks overdue tasks and dispatches alerts to employee + PM
Schedule::command('tasks:mark-overdue')->hourly();
