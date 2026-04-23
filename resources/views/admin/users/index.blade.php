@extends('layouts.admin')

@section('title', 'User Management')
@section('title_prefix', 'GYM')
@section('title_suffix', 'ADMINS')

@section('header_actions')
<a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">← Dashboard</a>
@endsection

@section('content')
<div class="card">
    <div style="margin-bottom: 2rem;">
        <h3 style="font-family: 'Oswald', sans-serif; text-transform: uppercase;">ADMINISTRATORS</h3>
        <p style="opacity: 0.6; font-size: 0.9rem;">Manage team members with access to this panel.</p>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Created At</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td style="font-weight: 600;">{{ $user->name }} @if($user->id === auth()->id()) <span style="background: var(--gym-yellow); color: #000; font-size: 0.6rem; padding: 0.1rem 0.4rem; border-radius: 1rem; margin-left: 0.5rem;">YOU</span> @endif</td>
                    <td style="opacity: 0.8;">{{ $user->email }}</td>
                    <td style="opacity: 0.5; font-size: 0.85rem;">{{ $user->created_at->format('d M Y') }}</td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-ghost" style="border: 1px solid rgba(255,193,7,0.4); background: rgba(255,193,7,0.05); padding: 0.6rem 1rem; font-size: 0.75rem; border-radius: 0.6rem;">
                             <i class="fas fa-lock" style="margin-right: 0.4rem; color: var(--gym-yellow);"></i> PASSWORD
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
