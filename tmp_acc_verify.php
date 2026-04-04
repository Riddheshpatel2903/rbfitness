<?php

use App\Models\Member;
use App\Models\Plan;
use App\Services\PaymentService;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- STARTING ACCOUNTING VERIFICATION ---\n";

$plan = Plan::first();

// 1. Register member (Debt -1000, Expired)
echo "Step 1: Enrolment...\n";
$m = Member::create([
    'name' => 'Acc Test',
    'phone' => '1111111111',
    'member_code' => 'ACC' . rand(100, 999),
    'plan_id' => $plan->id,
    'join_date' => now()->toDateString(),
    'expiry_date' => now()->toDateString(),
    'status' => 'expired',
    'balance' => -$plan->price
]);

// 2. Dashboard Count (Should include this member)
$totalDues = Member::where('balance', '<', 0)->sum('balance');
echo "Initial Total Dues in System: {$totalDues}\n";

// 3. Partial Payment (500) -> Becomes Active but STILL owes 500
echo "\nStep 2: Partial Payment (500)...\n";
$ps = app(PaymentService::class);
$ps->recordPayment($m, $plan->id, 500, now()->toDateString());
$m->refresh();

echo "Status: {$m->status}, Balance: {$m->balance}\n";
$newTotalDues = Member::where('balance', '<', 0)->sum('balance');
echo "New Total Dues in System: {$newTotalDues}\n";

if ($m->status == 'active' && $m->balance == -500 && $newTotalDues < 0) {
    echo "✅ Correct: Active member with partial payment STILL contributes to Total Dues.\n";
} else {
    echo "❌ Error in Dues accounting logic.\n";
}

echo "\nStep 3: Verification of Plan ID in Payment...\n";
$payment = $m->payments()->latest()->first();
echo "Payment Plan ID: " . ($payment->plan_id ?? 'NULL') . "\n";
if ($payment->plan_id == $plan->id) {
    echo "✅ Payment record correctly tracked the Plan ID.\n";
} else {
    echo "❌ Payment missing Plan ID tracking!\n";
}

echo "\n--- VERIFICATION COMPLETE ---\n";
$m->delete();
