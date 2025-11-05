<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule notification commands
Schedule::command('reviews:send-reminders')
    ->dailyAt('08:00')
    ->onOneServer()
    ->withoutOverlapping()
    ->description('Send review reminders to reviewers 3 days before deadline');

Schedule::command('reviews:check-overdue')
    ->hourly()
    ->onOneServer()
    ->withoutOverlapping()
    ->description('Check and send notifications for overdue reviews');

Schedule::command('reports:send-daily-summary')
    ->dailyAt('08:00')
    ->onOneServer()
    ->withoutOverlapping()
    ->description('Send daily summary reports to role-specific users');

Schedule::command('reports:send-weekly-summary')
    ->weeklyOn(1, '08:00')  // Monday at 08:00
    ->onOneServer()
    ->withoutOverlapping()
    ->description('Send weekly summary reports to role-specific users');
