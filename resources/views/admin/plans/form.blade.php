@extends('layouts.admin')

@section('title', isset($plan) ? 'Edit Plan' : 'Create Plan')
@section('title_prefix', 'MEMBERSHIP')
@section('title_suffix', isset($plan) ? 'EDIT' : 'CREATE')

@section('header_actions')
<a href="{{ route('admin.plans.index') }}" class="btn btn-ghost">← Back</a>
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="{{ isset($plan) ? route('admin.plans.update', $plan->id) : route('admin.plans.store') }}" method="POST">
        @csrf
        @if(isset($plan))
            @method('PUT')
        @endif

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Category</label>
            <select name="category_id" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <option value="" disabled selected>Select a category...</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $plan->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Plan Name</label>
            <input type="text" name="name" value="{{ old('name', $plan->name ?? '') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. Monthly Standard" required>
            @error('name')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Duration (Days)</label>
                <input type="number" name="duration_days" value="{{ old('duration_days', $plan->duration_days ?? '') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. 30" required>
                @error('duration_days')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Price (₹)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $plan->price ?? '') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. 1000" required>
                @error('price')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
            </div>
        </div>
        
        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Description (Bullet Points)</label>
            <textarea name="description" rows="5" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem; resize: vertical;" placeholder="Enter each feature on a new line&#10;e.g.&#10;Gym Floor Access&#10;Expert Guidance&#10;Personal Trainer">{{ old('description', $plan->description ?? '') }}</textarea>
            <p style="font-size: 0.75rem; opacity: 0.4; margin-top: 0.5rem;">Note: Each line will be displayed as a bullet point on the website.</p>
            @error('description')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>
        <div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; accent-color: var(--gym-yellow); cursor: pointer;">
            <label for="is_active" style="font-size: 0.9rem; color: #fff; cursor: pointer; text-transform: uppercase; font-weight: 600; opacity: 0.8;">Is Active?</label>
            @error('is_active')<p style="color: #ff4d4d; font-size: 0.8rem; margin-left: 1rem;">{{ $message }}</p>@enderror
        </div>

        <div style="display: flex; justify-content: center;">
            <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; min-width: 200px;">
                {{ isset($plan) ? 'UPDATE PLAN' : 'CREATE PLAN' }}
            </button>
        </div>
    </form>
</div>
@endsection
