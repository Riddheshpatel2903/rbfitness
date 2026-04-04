<?php

namespace App\Jobs;

use App\Services\MemberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateMemberStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(MemberService $memberService): void
    {
        Log::info("Starting daily member status update job.");
        $memberService->updateAllStatuses();
        Log::info("Completed daily member status update job.");
    }
}
