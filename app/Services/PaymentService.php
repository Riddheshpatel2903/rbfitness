<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Payment;
use App\Models\Plan;
use App\Services\MemberService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * Record a payment and update member's expiry date and status.
     * 
     * @param Member $member
     * @param int $planId
     * @param float $amount
     * @param string $paymentDate
     * @return Payment
     */
    public function recordPayment(Member $member, $planId, $amount, $paymentDate)
    {
        return DB::transaction(function () use ($member, $planId, $amount, $paymentDate) {
            $plan = Plan::findOrFail($planId);
            
            // Calculate new expiry date
            // Logic: 
            // 1. If member owes money (balance < 0), this is the Enrolment Period. 
            //    Expiry is anchored to JOIN_DATE.
            // 2. If member is paid up (balance >= 0), this is a Renewal. 
            //    Expiry is anchored to PAYMENT_DATE + Plan Duration.
            if ($member->balance < 0) {
                $newExpiry = Carbon::parse($member->join_date)->addDays($plan->duration_days);
            } else {
                $newExpiry = Carbon::parse($paymentDate)->addDays($plan->duration_days);
            }
            
            $newExpiryDate = $newExpiry->toDateString();

            // Create payment record
            $payment = Payment::create([
                'member_id' => $member->id,
                'plan_id' => $planId,
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'expiry_date' => $newExpiryDate,
            ]);

            // Update member record
            $member->expiry_date = $newExpiryDate;
            $member->plan_id = $planId; // Upgrading/Updating current plan
            
            // Fee adjustment: 
            // If member has a negative balance (debt from registration or previous expiry), 
            // the payment first clears the debt. A new plan charge is only incurred if they 
            // are already paid up or their payment exceeds the current debt.
            if ($member->balance < 0) {
                $member->balance += $amount;
                // If they've cleared their previous debt and are extending, 
                // we only subtract the plan price if they were 'active' or if this 
                // payment was intended to buy a NEW package. 
                // Actually, the simplest way: If they were in debt, the enrolment fee 
                // was already subtracted. So we don't subtract it again for the FIRST extension.
            } else {
                $member->balance = $member->balance + $amount - $plan->price;
            }
            
            $member->save();

            // Update status (extends expiry and flips to active if within date)
            $this->memberService->updateStatus($member);

            return $payment;
        });
    }
}
