<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Plan;
use Carbon\Carbon;

class MemberService
{
    /**
     * Calculate expiry date based on join date and plan duration.
     * 
     * @param string $joinDate
     * @param int $planId
     * @return string
     */
    public function calculateExpiryDate($joinDate, $planId)
    {
        $plan = Plan::findOrFail($planId);
        return Carbon::parse($joinDate)->addDays($plan->duration_days)->toDateString();
    }

    /**
     * Update member status based on current date and expiry date.
     * 
     * IF today <= expiry_date → active
     * IF today > expiry_date → expired
     * IF today > expiry_date + grace_days → blocked
     * 
     * @param Member $member
     * @return void
     */
    public function updateStatus(Member $member)
    {
        $today = Carbon::today();
        $expiryDate = Carbon::parse($member->expiry_date);
        $graceDate = $expiryDate->copy()->addDays($member->grace_days);

        if ($today->lte($expiryDate)) {
            $member->status = 'active';
        } elseif ($today->gt($graceDate)) {
            $member->status = 'blocked';
        } else {
            $member->status = 'expired';
        }

        $member->save();
    }

    /**
     * Update all member statuses.
     * 
     * @return void
     */
    public function updateAllStatuses()
    {
        $members = Member::all();
        foreach ($members as $member) {
            $this->updateStatus($member);
        }
    }
}
