<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $query = Plan::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $plans = $query->get();

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('admin.plans._table', compact('plans'))->render(),
                'total' => $plans->count(),
            ]);
        }

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $categories = \App\Models\PlanCategory::where('is_active', true)->get();
        return view('admin.plans.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:plan_categories,id',
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active');

        Plan::create($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        $categories = \App\Models\PlanCategory::where('is_active', true)->get();
        return view('admin.plans.edit', compact('plan', 'categories'));
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'category_id' => 'required|exists:plan_categories,id',
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active');

        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        // Check if plan is being used by members
        if ($plan->members()->count() > 0) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete plan as it is assigned to members.'], 400);
            }
            return back()->with('error', 'Cannot delete plan as it is assigned to members.');
        }

        $plan->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Plan deleted successfully.']);
        }

        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully.');
    }
}
