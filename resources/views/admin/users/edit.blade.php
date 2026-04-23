@extends('layouts.admin')

@section('title', 'Edit Admin Account')
@section('title_prefix', 'GYM')
@section('title_suffix', 'ACCOUNT SETTINGS')

@section('header_actions')
<a href="{{ route('admin.users.index') }}" class="btn btn-ghost">← Back</a>
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <h3 style="font-family: 'Oswald', sans-serif; text-transform: uppercase;">Manage Account</h3>
        <p style="opacity: 0.6; font-size: 0.9rem;">Update credentials for <strong>{{ $user->name }}</strong>.</p>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
            @error('name')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
            @error('email')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="background: rgba(255,193,7,0.05); padding: 1.5rem; border-radius: 1rem; border: 1px solid rgba(255,193,7,0.1); margin-bottom: 2rem;">
            <h4 style="font-size: 0.9rem; margin-top: 0; color: var(--gym-yellow); text-transform: uppercase; letter-spacing: 0.05em;">Change Password</h4>
            <p style="font-size: 0.75rem; opacity: 0.6; margin-bottom: 1.5rem;">Leave blank to keep the current password.</p>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.7rem; text-transform: uppercase; opacity: 0.5; margin-bottom: 0.5rem;">New Password</label>
                <input type="password" name="password" style="width: 100%; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; padding: 0.8rem; color: #fff; font-size: 0.9rem;">
                @error('password')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-bottom: 0;">
                <label style="display: block; font-size: 0.7rem; text-transform: uppercase; opacity: 0.5; margin-bottom: 0.5rem;">Confirm New Password</label>
                <input type="password" name="password_confirmation" style="width: 100%; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; padding: 0.8rem; color: #fff; font-size: 0.9rem;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.25rem; font-weight: 700;">
             UPDATE ACCOUNT
        </button>
    </form>
</div>
@endsection
