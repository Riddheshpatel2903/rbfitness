<?php

namespace App\Services;

use Twilio\Rest\Client;
use App\Models\Member;
use App\Models\WhatsAppLog;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = trim(env('TWILIO_AUTH_TOKEN')); // Trim to remove any quotes/spaces from .env
        $this->from = env('TWILIO_WHATSAPP_FROM');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Send WhatsApp message via Twilio
     *
     * @param string|Member $to Recipient phone number (E.164) or Member model
     * @param string $message The message body
     * @param int|null $memberId For logging
     * @param string|null $type For logging (e.g., 2_day_reminder)
     * @return bool
     */
    public function sendMessage($to, $message, $memberId = null, $type = null)
    {
        // Handle Member model being passed
        if ($to instanceof \App\Models\Member) {
            $memberId = $to->id;
            $to = $to->phone;
        }

        if (!$this->client) {
            Log::error("Twilio client not initialized. Check .env credentials.");
            return false;
        }

        // Ensure "whatsapp:" prefix for both from and to
        $formattedTo = str_starts_with($to, 'whatsapp:') ? $to : "whatsapp:$to";

        try {
            $this->client->messages->create($formattedTo, [
                'from' => $this->from,
                'body' => $message
            ]);

            if ($memberId) {
                WhatsAppLog::create([
                    'member_id' => $memberId,
                    'type' => $type,
                    'message' => $message,
                    'status' => 'sent'
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("WhatsApp Sending Error: " . $e->getMessage());
            
            if ($memberId) {
                WhatsAppLog::create([
                    'member_id' => $memberId,
                    'type' => $type,
                    'message' => $message,
                    'status' => 'failed'
                ]);
            }
            
            return false;
        }
    }
}
