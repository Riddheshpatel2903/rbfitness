<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Plan;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_members' => Member::count(),
            'active_members' => Member::where('status', 'active')->count(),
            'total_plans' => Plan::count(),
            'recent_payments' => Payment::with('member')->latest()->take(5)->get(),
            'expiring_soon' => Member::where('status', 'active')
                ->where('expiry_date', '<=', now()->addDays(7))
                ->orderBy('expiry_date')
                ->take(5)->get(),
            'expired_members' => Member::where('status', 'expired')
                ->orderBy('expiry_date', 'desc')
                ->take(5)->get(),
            'total_dues' => Member::where('balance', '<', 0)->sum('balance'),
            'total_advance' => Member::where('balance', '>', 0)->sum('balance'),
            'due_members' => Member::withCount('payments')
                ->where('balance', '<', 0)
                ->orderBy('balance', 'asc')
                ->take(5)->get(),
        ];

        if (request()->ajax()) {
            return response()->json($stats);
        }

        return view('admin.dashboard', compact('stats'));
    }
}
