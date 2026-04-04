@extends('layouts.admin')

@section('title', 'Members Management')
@section('title_prefix', 'GYM')
@section('title_suffix', 'MEMBERS')

@section('header_actions')
<a href="{{ route('admin.members.create') }}" class="btn btn-primary">+ REGISTER NEW MEMBER</a>
@endsection

@section('content')
<div class="card">
    <div class="filter-container" style="margin-bottom: 2rem;">
        <form action="{{ route('admin.members.index') }}" method="GET" class="filter-container">
            <input type="text" name="search" value="{{ request('search') }}" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: #fff;" placeholder="Search Name or Code...">
            <button type="submit" class="btn btn-ghost">Filter</button>
        </form>
    </div>

    <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th class="hide-mobile">Code</th>
                <th>Name</th>
                <th class="hide-mobile">Plan</th>
                <th>Expiry</th>
                <th class="hide-mobile">Status</th>
                <th class="hide-mobile">Fees Balance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
            <tr>
                <td class="hide-mobile"><span style="font-family: monospace; opacity: 0.7;">{{ $member->member_code }}</span></td>
                <td style="font-weight: 600;">{{ $member->name }}</td>
                <td class="hide-mobile">{{ $member->plan?->name }}</td>
                <td style="color: {{ \Carbon\Carbon::parse($member->expiry_date)->isPast() ? '#ff4d4d' : '#fff' }}">{{ $member->expiry_date }}</td>
                <td class="hide-mobile"><span class="status-badge bg-{{ $member->status }}">{{ $member->status }}</span></td>
                <td class="hide-mobile">
                    @php
                        $isDue = ($member->status != 'active' || $member->payments->count() == 0) && $member->balance < 0;
                    @endphp
                    
                    @if($member->balance < 0)
                        @if($isDue)
                            <span style="color: #ff4d4d; font-weight: 600; font-size: 0.9rem;">Due: ₹{{ number_format(abs($member->balance), 2) }}</span>
                        @else
                            <span style="opacity: 0.6; font-size: 0.9rem;">Remaining: ₹{{ number_format(abs($member->balance), 2) }}</span>
                        @endif
                    @elseif($member->balance > 0)
                        <span style="color: #00ff88; font-weight: 600; font-size: 0.9rem;">Advance: ₹{{ number_format($member->balance, 2) }}</span>
                    @else
                        <span style="opacity: 0.5; font-size: 0.9rem;">Paid</span>
                    @endif
                </td>
                <td>
                    <div class="actions-stack">
                        <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
                        <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Delete this member?')" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.1); width: 100%;">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; opacity: 0.5; padding: 3rem;">No members found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 2rem;">
        {{ $members->links() }}
    </div>
</div>
@endsection
