@extends('layouts.admin')

@section('title', isset($trainer) ? 'Edit Trainer' : 'Add Trainer')
@section('title_prefix', 'GYM')
@section('title_suffix', isset($trainer) ? 'EDIT TRAINER' : 'ADD TRAINER')

@section('header_actions')
<a href="{{ route('admin.trainers.index') }}" class="btn btn-ghost">← BACK TO TRAINERS</a>
@endsection

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <form action="{{ isset($trainer) ? route('admin.trainers.update', @$trainer->id) : route('admin.trainers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($trainer))
            @method('PUT')
        @endif

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Trainer Name</label>
            <input type="text" name="name" value="{{ old('name', @$trainer->name ?? '') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. Akshat Patel" required>
            @error('name')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Specialization</label>
            <input type="text" name="specialization" value="{{ old('specialization', @$trainer->specialization ?? '') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. Bodybuilding, Yoga, Strength" required>
            @error('specialization')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Short Bio</label>
            <textarea name="bio" rows="4" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="A brief description of the trainer's expertise...">{{ old('bio', @$trainer->bio ?? '') }}</textarea>
            @error('bio')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 3rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Profile Image (Optional)</label>
            <input type="file" name="image" style="width: 100%; color: rgba(255,255,255,0.6);">
            @if(isset($trainer) && @$trainer->image)
                <p style="font-size: 0.8rem; opacity: 0.6; margin-top: 0.5rem;">Current: {{ $trainer->image }}</p>
            @endif
            @error('image')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            {{ isset($trainer) ? 'UPDATE TRAINER' : 'CONFIRM & ADD' }}
        </button>
    </form>
</div>
@endsection
