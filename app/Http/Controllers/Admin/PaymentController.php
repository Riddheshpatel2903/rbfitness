<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $payments = Payment::with(['member', 'plan'])->latest()->paginate(15);
        return view('admin.payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $members = Member::all();
        $plans = \App\Models\Plan::where('is_active', true)->get();
        $selectedMember = $request->member_id ? Member::with('plan')->find($request->member_id) : null;
        return view('admin.payments.create', compact('members', 'selectedMember', 'plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'plan_id' => 'required|exists:plans,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $member = Member::findOrFail($request->member_id);
        
        $this->paymentService->recordPayment(
            $member, 
            $request->plan_id,
            $request->amount, 
            $request->payment_date
        );

        return redirect()->route('admin.payments.index')->with('success', 'Payment recorded and membership extended.');
    }
}
