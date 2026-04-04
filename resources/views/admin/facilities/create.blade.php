@extends('layouts.admin')

@section('title', isset($facility) ? 'Edit Facility' : 'Add Facility')
@section('title_prefix', 'GYM')
@section('title_suffix', isset($facility) ? 'EDIT FACILITY' : 'ADD FACILITY')

@section('header_actions')
<a href="{{ route('admin.facilities.index') }}" class="btn btn-ghost">← BACK TO FACILITIES</a>
@endsection

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <form action="{{ isset($facility) ? route('admin.facilities.update', $facility->id) : route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($facility))
            @method('PUT')
        @endif

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Facility Title</label>
            <input type="text" name="title" value="{{ old('title', $facility->title ?? '') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. Cardio Zone" required>
            @error('title')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Description</label>
            <textarea name="description" rows="4" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="Optional description...">{{ old('description', $facility->description ?? '') }}</textarea>
            @error('description')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 3rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Image or Video</label>
            <input type="file" name="image" style="width: 100%; color: rgba(255,255,255,0.6);">
            @if(isset($facility) && $facility->image)
                <p style="font-size: 0.8rem; opacity: 0.6; margin-top: 0.5rem;">Current file: {{ $facility->image }}</p>
            @endif
            <p style="font-size: 0.75rem; opacity: 0.4; margin-top: 0.5rem;">Accepted formats: JPEG, PNG, MP4, MOV</p>
            @error('image')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            {{ isset($facility) ? 'UPDATE FACILITY' : 'CONFIRM & ADD' }}
        </button>
    </form>
</div>
@endsection
