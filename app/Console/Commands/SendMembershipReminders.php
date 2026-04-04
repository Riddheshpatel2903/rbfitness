<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\WhatsAppLog;
use App\Services\WhatsAppService;
use Carbon\Carbon;

#[Signature('gym:send-reminders')]
#[Description('Send automated WhatsApp reminders for membership expirations')]
class SendMembershipReminders extends Command
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $inTwoDays = Carbon::today()->addDays(2)->toDateString();
        $fiveDaysAgo = Carbon::today()->subDays(5)->toDateString();

        $this->info("Scanning for membership reminders...");
        $this->info("Today: {$today}");
        $this->info("In 2 Days: {$inTwoDays}");
        $this->info("5 Days Ago: {$fiveDaysAgo}");

        // 1. Expiring in 2 days: "about to expire"
        $this->processReminders($inTwoDays, '2_day_reminder', 'Hi {name}, your RB Fitness plan is about to expire in 2 days.');

        // 2. Expired today: "plan expired"
        $this->processReminders($today, 'expiry_notice', 'Hi {name}, your RB Fitness plan expired today. Please renew to continue.');

        // 3. 5 days after expiry: "membership amendement"
        $this->processReminders($fiveDaysAgo, '5_day_notice', 'Hi {name}, your membership is pending amendment. Please visit the gym.');

        $this->info("Reminders sent successfully.");
    }

    protected function processReminders($date, $type, $messageTemplate)
    {
        $members = Member::whereDate('expiry_date', $date)->get();

        foreach ($members as $member) {
            // Check if reminder of this type was already sent for this specific expiry cycle
            $alreadySent = WhatsAppLog::where('member_id', $member->id)
                ->where('type', $type)
                ->where('created_at', '>', Carbon::today()->subDays(10)) // Limit check to current cycle
                ->where('status', 'sent')
                ->exists();

            if (!$alreadySent) {
                $message = str_replace('{name}', $member->name, $messageTemplate);
                
                // Ensure phone has + prefix for E.164
                $phone = $member->phone;
                if (!str_starts_with($phone, '+')) {
                    if (str_starts_with($phone, '91') && strlen($phone) >= 12) {
                        $phone = '+' . $phone;
                    } else {
                        $phone = '+91' . $phone;
                    }
                }

                $this->whatsappService->sendMessage($phone, $message, $member->id, $type);
                $this->info("Sent {$type} to {$member->name}");
            }
        }
    }
}
