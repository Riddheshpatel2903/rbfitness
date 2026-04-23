@extends('layouts.admin')

@section('title', isset($facility) ? 'Edit Facility' : 'Add Facility')
@section('title_prefix', 'GYM')
@section('title_suffix', isset($facility) ? 'EDIT FACILITY' : 'ADD FACILITY')

@section('header_actions')
<a href="{{ route('admin.facilities.index') }}" class="btn btn-ghost">← Back</a>
@endsection

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <form action="{{ isset($facility) ? route('admin.facilities.update', @$facility->id) : route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($facility))
            @method('PUT')
        @endif

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Facility Title</label>
            <input type="text" name="title" value="{{ old('title', @$facility->title ?? '') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. Cardio Zone" required>
            @error('title')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Description</label>
            <textarea name="description" rows="4" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="Optional description...">{{ old('description', @$facility->description ?? '') }}</textarea>
            @error('description')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 3rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Image or Video</label>
            <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1rem;">
                @if(isset($facility) && @$facility->image)
                    @php $ext = pathinfo($facility->image, PATHINFO_EXTENSION); @endphp
                    @if(in_array(strtolower($ext), ['mp4', 'mov', 'MOV']))
                         <div style="height: 80px; width: 80px; background: rgba(255,193,7,0.1); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255,193,7,0.3);">
                            <i class="fas fa-play-circle" style="color: var(--gym-yellow); font-size: 1.5rem;"></i>
                        </div>
                    @else
                        <img src="{{ \App\Helpers\MediaHelper::getUrl($facility->image) }}" alt="Preview" style="height: 80px; width: 80px; object-fit: cover; border-radius: 0.75rem; border: 2px solid rgba(255,193,7,0.3);">
                    @endif
                @endif
                <input type="file" name="image" style="flex: 1; color: rgba(255,255,255,0.6); background: rgba(255,255,255,0.03); padding: 0.75rem; border-radius: 0.5rem; border: 1px dashed rgba(255,255,255,0.1);">
            </div>
            <p style="font-size: 0.75rem; opacity: 0.4;">Accepted formats: JPEG, PNG, MP4, MOV. Max 50MB for video.</p>
            @error('image')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            {{ isset($facility) ? 'UPDATE FACILITY' : 'CONFIRM & ADD' }}
        </button>
    </form>
</div>
@endsection
