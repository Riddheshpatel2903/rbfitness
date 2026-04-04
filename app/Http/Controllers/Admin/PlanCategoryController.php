<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanCategoryController extends Controller
{
    public function index()
    {
        $categories = PlanCategory::withCount('plans')->get();
        return view('admin.plan_categories.index', compact('categories'));
    }

    public function toggleStatus(PlanCategory $planCategory)
    {
        $planCategory->update([
            'is_active' => !$planCategory->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $planCategory->is_active,
            'message' => 'Category status updated successfully.'
        ]);
    }

    public function create()
    {
        return view('admin.plan_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:plan_categories,name',
        ]);

        PlanCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.plan_categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(PlanCategory $planCategory)
    {
        return view('admin.plan_categories.edit', compact('planCategory'));
    }

    public function update(Request $request, PlanCategory $planCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:plan_categories,name,' . $planCategory->id,
        ]);

        $planCategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.plan_categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(PlanCategory $planCategory)
    {
        if ($planCategory->plans()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated plans.');
        }

        $planCategory->delete();
        return redirect()->route('admin.plan_categories.index')->with('success', 'Category deleted successfully.');
    }
}
