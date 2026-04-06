@extends('layouts.admin')

@section('title', isset($planCategory) ? 'Edit Category' : 'Add Category')
@section('title_prefix', 'GYM')
@section('title_suffix', isset($planCategory) ? 'EDIT CATEGORY' : 'ADD CATEGORY')

@section('header_actions')
<a href="{{ route('admin.plan_categories.index') }}" class="btn btn-ghost">← Back</a>
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="{{ isset($planCategory) ? route('admin.plan_categories.update', $planCategory->id) : route('admin.plan_categories.store') }}" method="POST">
        @csrf
        @if(isset($planCategory))
            @method('PUT')
        @endif

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Category Name</label>
            <input type="text" name="name" value="{{ old('name', $planCategory->name ?? '') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. Monthly" required>
            @error('name')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: flex; align-items: center; gap: 1rem; cursor: pointer;">
                <input type="checkbox" name="is_active" {{ old('is_active', $planCategory->is_active ?? true) ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; accent-color: var(--gym-yellow);">
                <span style="font-size: 1rem; color: #fff; opacity: 0.8;">Is Active? (Show on Frontend)</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            {{ isset($planCategory) ? 'UPDATE CATEGORY' : 'CONFIRM & ADD' }}
        </button>
    </form>
</div>
@endsection
