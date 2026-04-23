<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::query();

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $facilities = $query->get();

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('admin.facilities._table', compact('facilities'))->render(),
                'total' => $facilities->count(),
            ]);
        }

        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('admin.facilities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp,mp4,mov,MOV|max:51200', // 50MB
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            $data['image'] = \App\Helpers\MediaHelper::upload($request->file('image'), 'facilities');
        }

        Facility::create($data);

        return redirect()->route('admin.facilities.index')->with('success', 'Facility added to CMS.');
    }

    public function edit(Facility $facility)
    {
        return view('admin.facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp,mp4,mov,MOV|max:51200', // 50MB
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            if ($facility->image) {
                \App\Helpers\MediaHelper::delete($facility->image);
            }
            $data['image'] = \App\Helpers\MediaHelper::upload($request->file('image'), 'facilities');
        }

        $facility->update($data);

        return redirect()->route('admin.facilities.index')->with('success', 'Facility updated correctly.');
    }

    public function destroy(Facility $facility)
    {
        if ($facility->image) {
            \App\Helpers\MediaHelper::delete($facility->image);
        }
        $facility->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Facility removed from CMS.']);
        }

        return redirect()->route('admin.facilities.index')->with('success', 'Facility removed from CMS.');
    }
}
