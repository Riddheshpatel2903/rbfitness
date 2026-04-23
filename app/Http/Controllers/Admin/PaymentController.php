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

    public function index(Request $request)
    {
        $query = Payment::with(['member', 'plan']);

        if ($request->search) {
            $search = strtolower($request->search);
            $query->whereHas('member', function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(member_code) LIKE ?', ["%{$search}%"]);
            });
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        // Calculate Stats for the top bar
        $stats = [
            'monthly_collected' => Payment::whereMonth('payment_date', now()->month)
                                        ->whereYear('payment_date', now()->year)
                                        ->sum('amount'),
            'paid_members'      => Member::where('balance', '>=', 0)->count(),
            'pending_members'   => Member::where('balance', '<', 0)->count(),
            'half_paid_members' => Member::where('balance', '<', 0)
                                        ->whereHas('payments')
                                        ->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('admin.payments._table', compact('payments'))->render(),
                'pagination' => $payments->links()->render(),
                'total' => $payments->total(),
                'stats' => $stats, // Pass stats for AJAX refresh if needed
            ]);
        }

        return view('admin.payments.index', compact('payments', 'stats'));
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

        $msg = 'Payment recorded and membership extended.';
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.payments.index')->with('success', $msg);
    }

    public function exportCsv()
    {
        $payments = Payment::with('member', 'plan')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->latest()
            ->get();

        $filename = "payments_report_" . now()->format('Y_m') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Date', 'Member Code', 'Member Name', 'Plan', 'Amount (INR)', 'New Expiry'];

        $callback = function() use($payments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_date->format('d-m-Y'),
                    $payment->member->member_code,
                    $payment->member->name,
                    $payment->plan?->name ?: 'N/A',
                    number_format($payment->amount, 2),
                    $payment->expiry_date->format('d-m-Y')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
