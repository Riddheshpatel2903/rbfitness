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
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('trainers', 'public');
            $data['image'] = $path;
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
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            if ($trainer->image) {
                Storage::disk('public')->delete($trainer->image);
            }
            $path = $request->file('image')->store('trainers', 'public');
            $data['image'] = $path;
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
