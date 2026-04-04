<?php

use App\Models\Member;
use App\Models\Plan;
use App\Services\PaymentService;
use Carbon\Carbon;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- STARTING LATE PAYMENT (FIXED PERIOD) VERIFICATION ---\n";

$plan = Plan::first();
if (!$plan) {
    echo "No plans found. Seeding...\n";
    $kernel->call('db:seed');
    $plan = Plan::first();
}

$joinDate = '2026-04-01'; // Join on April 1
$expectedEnrolmentExpiry = '2026-05-01'; // 30 days later

// 1. Setup Member as if just registered
echo "Step 1: Enrolment on April 1...\n";
$m = Member::create([
    'name' => 'Late Pay Test',
    'phone' => '8888888888',
    'member_code' => 'LP' . rand(100, 999),
    'plan_id' => $plan->id,
    'join_date' => $joinDate,
    'expiry_date' => $joinDate, // New registration logic
    'status' => 'expired',
    'balance' => -$plan->price
]);

// 2. Late First Payment (April 28)
echo "Step 2: Recording First Payment on April 28th (LATE)...\n";
$ps = app(PaymentService::class);
$ps->recordPayment($m, $plan->id, $plan->price, '2026-04-28');
$m->refresh();

echo "New Expiry Date: {$m->expiry_date}\n";
if ($m->expiry_date == $expectedEnrolmentExpiry) {
    echo "✅ Success: First payment anchored to JOIN DATE. Expiry is May 1st.\n";
} else {
    echo "❌ Error: Expiry was shifted! It is {$m->expiry_date} instead of {$expectedEnrolmentExpiry}\n";
}

// 3. Renewal Payment (May 5)
echo "\nStep 3: Recording Renewal on May 5th...\n";
$ps->recordPayment($m, $plan->id, $plan->price, '2026-05-05');
$m->refresh();

$expectedRenewalExpiry = Carbon::parse('2026-05-05')->addDays($plan->duration_days)->toDateString();

echo "New Expiry Date: {$m->expiry_date}\n";
if ($m->expiry_date == $expectedRenewalExpiry) {
    echo "✅ Success: Renewal extended from PAYMENT DATE ({$expectedRenewalExpiry}).\n";
} else {
    echo "❌ Error in Renewal Logic!\n";
}

echo "\n--- VERIFICATION COMPLETE ---\n";
$m->delete();
