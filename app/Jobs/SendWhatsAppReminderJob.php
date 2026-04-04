<?php

namespace App\Jobs;

use App\Models\Member;
use App\Models\Setting;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(WhatsAppService $whatsAppService): void
    {
        Log::info("Starting daily WhatsApp reminder job.");
        
        $reminderDays = 3;
        $targetDate = Carbon::now()->addDays($reminderDays)->toDateString();
        
        $members = Member::where('expiry_date', $targetDate)
                         ->where('status', 'active')
                         ->get();

        Log::info("Found " . $members->count() . " members expiring on " . $targetDate);

        $gymName = Setting::where('key', 'gym_name')->first()->value ?? 'RB Fitness Club';

        foreach ($members as $member) {
            /** @var Member $member */
            $message = "Hello {$member->name}, this is a friendly reminder from {$gymName}. Your membership is expiring on {$member->expiry_date} (in 3 days). Please visit the gym to renew and stay fit! 💪";
            $whatsAppService->sendMessage($member, $message);
        }

        Log::info("Completed daily WhatsApp reminder job.");
    }
}
