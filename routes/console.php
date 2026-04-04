<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use App\Jobs\UpdateMemberStatusJob;
use App\Jobs\SendWhatsAppReminderJob;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new UpdateMemberStatusJob)->dailyAt('00:00');
Schedule::command('gym:send-reminders')->dailyAt('09:00');
