<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Plan;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(member_code) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $members = $query->latest()->paginate(20)->withQueryString();

        // AJAX request: return rendered partials as JSON
        if ($request->ajax()) {
            return response()->json([
                'rows'       => view('admin.members._table', compact('members'))->render(),
                'pagination' => $members->links()->render(),
                'total'      => $members->total(),
            ]);
        }

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

        $msg = 'Member registered successfully.';
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.members.index')->with('success', $msg);
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

        $msg = 'Member updated successfully.';
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.members.index')->with('success', $msg);
    }

    public function destroy(Member $member)
    {
        $member->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Member deleted.']);
        }

        return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully.');
    }

    // -------------------------------------------------------
    // CSV Import
    // -------------------------------------------------------
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        // Use the first plan as default for imported legacy members
        $defaultPlan = Plan::first();
        if (! $defaultPlan) {
            return back()->with('error', 'No plans found. Please create a plan first before importing members.');
        }

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Read header row
        $headers = fgetcsv($handle);
        // Normalize headers: lowercase, trim
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        // Get last member code number
        $lastMember = Member::orderBy('id', 'desc')->first();
        $nextNumber = 1;
        if ($lastMember) {
            preg_match('/\d+$/', $lastMember->member_code, $matches);
            $nextNumber = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
        }

        while (($row = fgetcsv($handle)) !== false) {
            // Map row to associative array
            $data = array_combine($headers, $row);

            $name  = trim($data['name']  ?? '');
            $phone = trim($data['phone'] ?? '0000000000');

            if (! $name) {
                $skipped++;
                continue;
            }

            // Clean phone — only digits, ensure not empty
            $phoneClean = preg_replace('/[^0-9]/', '', $phone);
            if (! $phoneClean) {
                $phoneClean = '0000000000';
            }

            // Parse renewal date (dd-MM-yyyy) → expiry_date
            $renewalRaw = trim($data['renewaldate'] ?? $data['renewal_date'] ?? '');
            try {
                $expiryDate = $renewalRaw
                    ? Carbon::createFromFormat('d-m-Y', $renewalRaw)->format('Y-m-d')
                    : now()->format('Y-m-d');
            } catch (\Exception $e) {
                $expiryDate = now()->format('Y-m-d');
            }

            // Parse join/payment date
            $payRaw = trim($data['paymentdate'] ?? $data['payment_date'] ?? '');
            try {
                $joinDate = $payRaw
                    ? Carbon::createFromFormat('d-m-Y', $payRaw)->format('Y-m-d')
                    : $expiryDate;
            } catch (\Exception $e) {
                $joinDate = $expiryDate;
            }

            // Skip if member with same name already exists
            if (Member::where('name', $name)->exists()) {
                $skipped++;
                continue;
            }

            // Generate unique member code
            $memberCode = 'RB' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            while (Member::where('member_code', $memberCode)->exists()) {
                $nextNumber++;
                $memberCode = 'RB' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            // Determine status based on expiry
            $status = Carbon::parse($expiryDate)->isPast() ? 'expired' : 'active';

            Member::create([
                'name'        => $name,
                'phone'       => $phoneClean,
                'email'       => null,
                'member_code' => $memberCode,
                'plan_id'     => $defaultPlan->id,
                'join_date'   => $joinDate,
                'expiry_date' => $expiryDate,
                'status'      => $status,
                'balance'     => 0,
                'grace_days'  => 3,
            ]);

            $nextNumber++;
            $imported++;
        }

        fclose($handle);

        $msg = "Import complete: {$imported} members imported, {$skipped} skipped (duplicates / empty rows).";

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.members.index')->with('success', $msg);
    }

    // -------------------------------------------------------
    // Import from local file (active_members.csv in project root)
    // -------------------------------------------------------
    public function importLocalCsv()
    {
        $filePath = base_path('active_members.csv');

        if (! file_exists($filePath)) {
            return back()->with('error', 'Local file not found: active_members.csv does not exist in the project root.');
        }

        $defaultPlan = Plan::first();
        if (! $defaultPlan) {
            return back()->with('error', 'No plans found. Please create a plan first before importing members.');
        }

        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle);
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        $imported = 0;
        $skipped  = 0;

        $lastMember = Member::orderBy('id', 'desc')->first();
        $nextNumber = 1;
        if ($lastMember) {
            preg_match('/\d+$/', $lastMember->member_code, $matches);
            $nextNumber = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) { $skipped++; continue; }
            $data = array_combine($headers, $row);

            $name = trim($data['name'] ?? '');
            if (! $name) { $skipped++; continue; }

            $phoneClean = preg_replace('/[^0-9]/', '', $data['phone'] ?? '');
            if (! $phoneClean) { $phoneClean = '0000000000'; }

            $renewalRaw = trim($data['renewaldate'] ?? '');
            try {
                $expiryDate = $renewalRaw ? Carbon::createFromFormat('d-m-Y', $renewalRaw)->format('Y-m-d') : now()->format('Y-m-d');
            } catch (\Exception $e) {
                $expiryDate = now()->format('Y-m-d');
            }

            $payRaw = trim($data['paymentdate'] ?? '');
            try {
                $joinDate = $payRaw ? Carbon::createFromFormat('d-m-Y', $payRaw)->format('Y-m-d') : $expiryDate;
            } catch (\Exception $e) {
                $joinDate = $expiryDate;
            }

            if (Member::where('name', $name)->exists()) { $skipped++; continue; }

            $memberCode = 'RB' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            while (Member::where('member_code', $memberCode)->exists()) {
                $nextNumber++;
                $memberCode = 'RB' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            $status = Carbon::parse($expiryDate)->isPast() ? 'expired' : 'active';

            Member::create([
                'name'        => $name,
                'phone'       => $phoneClean,
                'email'       => null,
                'member_code' => $memberCode,
                'plan_id'     => $defaultPlan->id,
                'join_date'   => $joinDate,
                'expiry_date' => $expiryDate,
                'status'      => $status,
                'balance'     => 0,
                'grace_days'  => 3,
            ]);

            $nextNumber++;
            $imported++;
        }

        fclose($handle);

        $msg = "Local CSV import complete: {$imported} members imported, {$skipped} skipped (duplicates / empty rows).";

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.members.index')->with('success', $msg);
    }
}
