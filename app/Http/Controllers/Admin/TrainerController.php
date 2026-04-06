<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainerController extends Controller
{
    public function index()
    {
        $trainers = Trainer::all();
        return view('admin.trainers.index', compact('trainers'));
    }

    public function create()
    {
        return view('admin.trainers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:10240', // 10MB
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            $data['image'] = \App\Helpers\MediaHelper::upload($request->file('image'), 'trainers');
        }

        Trainer::create($data);

        return redirect()->route('admin.trainers.index')->with('success', 'Trainer added to CMS.');
    }

    public function edit(Trainer $trainer)
    {
        return view('admin.trainers.edit', compact('trainer'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:10240', // 10MB
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            // Delete old file if it's local
            if ($trainer->image) {
                \App\Helpers\MediaHelper::delete($trainer->image);
            }
            $data['image'] = \App\Helpers\MediaHelper::upload($request->file('image'), 'trainers');
        }

        $trainer->update($data);

        return redirect()->route('admin.trainers.index')->with('success', 'Trainer updated correctly.');
    }

    public function destroy(Trainer $trainer)
    {
        if ($trainer->image) {
            Storage::disk('public')->delete($trainer->image);
        }
        $trainer->delete();
        return redirect()->route('admin.trainers.index')->with('success', 'Trainer removed from CMS.');
    }
}
