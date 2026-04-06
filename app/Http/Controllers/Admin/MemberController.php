<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Plan;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MemberController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function index(Request $request)
    {
        $query = Member::with('plan');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('member_code', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $members = $query->latest()->paginate(15);
        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        $plans = Plan::all();
        
        $lastMember = Member::orderBy('id', 'desc')->first();
        
        $nextNumber = 1;
        if ($lastMember) {
            preg_match('/\d+$/', $lastMember->member_code, $matches);
            $nextNumber = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
        }
        
        $nextCode = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('admin.members.create', compact('plans', 'nextCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'member_code' => 'required|string|unique:members,member_code',
            'plan_id' => 'required|exists:plans,id',
            'join_date' => 'required|date',
        ]);

        $data = $request->all();
        
        // Initial enrolment setup:
        // 1. Set debt equal to the plan price + ₹200 joining fee.
        // 2. Set expiry_date to join_date (effectively starting as expired/due).
        $plan = Plan::findOrFail($request->plan_id);
        $data['expiry_date'] = $request->join_date;
        $data['status'] = 'expired';
        $data['balance'] = -($plan->price + 200);

        Member::create($data);

        return redirect()->route('admin.members.index')->with('success', 'Member registered successfully.');
    }

    public function edit(Member $member)
    {
        $plans = Plan::all();
        return view('admin.members.edit', compact('member', 'plans'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'member_code' => 'required|string|unique:members,member_code,' . $member->id,
            'plan_id' => 'required|exists:plans,id',
            'join_date' => 'required|date',
            'expiry_date' => 'required|date',
            'status' => 'required|in:active,expired,blocked',
        ]);

        $member->update($request->all());

        return redirect()->route('admin.members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully.');
    }
}
