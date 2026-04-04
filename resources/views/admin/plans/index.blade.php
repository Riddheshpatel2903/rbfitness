@extends('layouts.admin')

@section('title', 'Membership Plans')
@section('title_prefix', 'GYM')
@section('title_suffix', 'PLANS')

@section('header_actions')
<a href="{{ route('admin.plans.create') }}" class="btn btn-primary">+ ADD NEW PLAN</a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th class="hide-mobile">ID</th>
                <th>Name</th>
                <th class="hide-mobile">Status</th>
                <th>Duration</th>
                <th>Price</th>
                <th class="hide-mobile">Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plans as $plan)
            <tr>
                <td class="hide-mobile">#{{ $plan->id }}</td>
                <td style="font-weight: 600;">{{ $plan->name }}</td>
                <td class="hide-mobile">
                    <span class="status-badge {{ $plan->is_active ? 'bg-active' : 'bg-blocked' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>{{ $plan->duration_days }} Days</td>
                <td style="color: var(--gym-yellow);">₹{{ number_format($plan->price, 2) }}</td>
                <td class="hide-mobile" style="font-size: 0.8rem; opacity: 0.7; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $plan->description ?? 'No description' }}
                </td>
                <td>
                    <div class="actions-stack">
                        <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
                        <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?')" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.1); width: 100%;">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; opacity: 0.5; padding: 3rem;">No plans found</td>
            </tr>
            @endforelse
        </tbody>
        </table>
    </div>
</div>
@endsection
