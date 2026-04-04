@extends('layouts.admin')

@section('title', 'Record Payment')
@section('title_prefix', 'NEW')
@section('title_suffix', 'PAYMENT')

@section('header_actions')
<a href="{{ route('admin.payments.index') }}" class="btn btn-ghost">← BACK TO PAYMENTS</a>
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="{{ route('admin.payments.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Select Plan</label>
            <select name="plan_id" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <option value="" disabled selected>Select a plan...</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ (old('plan_id') == $plan->id || (isset($selectedMember) && $selectedMember->plan_id == $plan->id)) ? 'selected' : '' }}>
                        {{ $plan->name }} (₹{{ number_format($plan->price, 2) }} - {{ $plan->duration_days }} Days)
                    </option>
                @endforeach
            </select>
            @error('plan_id')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Member</label>
            <select name="member_id" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <option value="" disabled selected>Select a member...</option>
                @foreach($members as $member)
                    @php
                        $balanceText = $member->balance < 0 ? 'Due: ₹' . number_format(abs($member->balance), 2) : ($member->balance > 0 ? 'Credit: ₹' . number_format($member->balance, 2) : 'Paid');
                        $balanceColor = $member->balance < 0 ? 'color: #ff4d4d;' : ($member->balance > 0 ? 'color: #00ff88;' : 'opacity: 0.7;');
                    @endphp
                    <option value="{{ $member->id }}" {{ (isset($selectedMember) && $selectedMember->id == $member->id) ? 'selected' : '' }}>
                        {{ $member->name }} ({{ $member->member_code }} - {{ $balanceText }})
                    </option>
                @endforeach
            </select>
            @error('member_id')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Amount Received (₹)</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. 1000" required>
            @error('amount')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 3rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Payment Date</label>
            <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
            @error('payment_date')<p style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <div class="card" style="background: rgba(255, 223, 0, 0.05); border-color: rgba(255, 223, 0, 0.1); padding: 1.5rem; margin-bottom: 2rem;">
            <p style="font-size: 0.875rem; line-height: 1.6; opacity: 0.8;">
                <strong>💡 Note:</strong> Recording this payment will automatically extend the member's expiry date based on their current plan duration and update their status.
            </p>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            CONFIRM & RECORD PAYMENT
        </button>
    </form>
</div>
@endsection
