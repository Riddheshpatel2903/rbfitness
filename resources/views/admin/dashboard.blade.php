@extends('layouts.admin')

@section('title', 'Dashboard')
@section('title_prefix', 'GYM')
@section('title_suffix', 'DASHBOARD')

@section('content')
<div class="grid-stats">
    <div class="card stat-card" style="position: relative; overflow: hidden;">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">TOTAL MEMBERS</p>
                <h2 class="stat-value">{{ $stats['total_members'] }}</h2>
            </div>
            <div class="stat-icon-wrapper" style="color: var(--gym-yellow);">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-indicator" style="background: linear-gradient(90deg, var(--gym-yellow), transparent);"></div>
    </div>

    <div class="card stat-card" style="position: relative; overflow: hidden;">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">ACTIVE MEMBERS</p>
                <h2 class="stat-value" style="color: #4dff4d;">{{ $stats['active_members'] }}</h2>
            </div>
            <div class="stat-icon-wrapper" style="color: #4dff4d;">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
        <div class="stat-indicator" style="background: linear-gradient(90deg, #4dff4d, transparent);"></div>
    </div>

    <div class="card stat-card" style="position: relative; overflow: hidden;">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">TOTAL PLANS</p>
                <h2 class="stat-value">{{ $stats['total_plans'] }}</h2>
            </div>
            <div class="stat-icon-wrapper" style="color: var(--gym-yellow);">
                <i class="fas fa-dumbbell"></i>
            </div>
        </div>
        <div class="stat-indicator" style="background: linear-gradient(90deg, var(--gym-yellow), transparent);"></div>
    </div>

    <div class="card stat-card" style="position: relative; overflow: hidden;">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">PENDING DUES</p>
                <h2 class="stat-value" style="color: #ff4d4d;">₹{{ number_format(abs($stats['total_dues']), 0) }}</h2>
            </div>
            <div class="stat-icon-wrapper" style="color: #ff4d4d;">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
        <div class="stat-indicator" style="background: linear-gradient(90deg, #ff4d4d, transparent);"></div>
    </div>

    <div class="card stat-card" style="position: relative; overflow: hidden;">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">ADVANCE PAID</p>
                <h2 class="stat-value" style="color: #00ff88;">₹{{ number_format($stats['total_advance'], 0) }}</h2>
            </div>
            <div class="stat-icon-wrapper" style="color: #00ff88;">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
        <div class="stat-indicator" style="background: linear-gradient(90deg, #00ff88, transparent);"></div>
    </div>
</div>

<!-- 1. Top Pending Dues (First Priority) -->
<div class="card card-accent-red" style="margin-top: 2rem;">
    <h3 style="font-family: 'Oswald', sans-serif; text-transform: uppercase; color: #ff4d4d;">Top Pending Dues</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Due Amount</th>
                    <th class="hide-mobile">Current Plan</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['due_members'] as $member)
                <tr>
                    <td style="font-weight: 600;">{{ $member->name }}</td>
                    <td style="color: #ff4d4d; font-weight: 600;">₹{{ number_format(abs($member->balance), 2) }}</td>
                    <td class="hide-mobile" style="opacity: 0.7;">{{ $member->plan?->name }}</td>
                    <td>
                        <div class="actions-stack">
                            <a href="{{ route('admin.payments.create', ['member_id' => $member->id]) }}" class="btn btn-primary" style="padding: 0.5rem 0.8rem; font-size: 0.7rem;" title="Collect Fees">
                                <i class="fas fa-money-bill-wave"></i> Collect
                            </a>
                            @if($member->phone)
                            <button onclick="sendSMS('{{ $member->phone }}', '{{ $member->name }}', '{{ $member->expiry_date ? \Carbon\Carbon::parse($member->expiry_date)->format('d M') : '' }}')" 
                                    class="btn-sms" 
                                    title="Send SMS">
                                <i class="fas fa-sms"></i> SMS
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; opacity: 0.5; padding: 3rem;">No pending dues! All members are paid up.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- 2. Expiring Soon -->
<div class="card card-accent-orange" style="margin-top: 2.5rem;">
    <h3 style="font-family: 'Oswald', sans-serif; text-transform: uppercase;">Expiring Soon</h3>
    <div class="table-responsive">
        <table style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Expiry</th>
                    <th style="text-align: right;">SMS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['expiring_soon'] as $member)
                <tr>
                    <td style="font-weight: 500;">{{ $member->name }}</td>
                    <td style="color: #ffb34d;">{{ \Carbon\Carbon::parse($member->expiry_date)->format('d M') }}</td>
                    <td style="text-align: right;">
                        @if($member->phone)
                        <button onclick="sendSMS('{{ $member->phone }}', '{{ $member->name }}', '{{ \Carbon\Carbon::parse($member->expiry_date)->format('d M') }}')" 
                                class="btn-sms" 
                                title="Send SMS">
                            <i class="fas fa-sms"></i> SMS
                        </button>
                        @else
                        <span style="opacity: 0.3;" title="Phone missing"><i class="fas fa-sms"></i></span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; opacity: 0.5; padding: 2rem;">No upcoming expiries</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- 3. Recently Expired -->
<div class="card card-accent-red" style="margin-top: 2.5rem;">
    <h3 style="font-family: 'Oswald', sans-serif; text-transform: uppercase; color: #ff4d4d;">Recently Expired</h3>
    <div class="table-responsive">
        <table style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Expired</th>
                    <th style="text-align: right;">SMS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['expired_members'] as $member)
                <tr>
                    <td style="font-weight: 500;">{{ $member->name }}</td>
                    <td style="color: #ff4d4d;">{{ \Carbon\Carbon::parse($member->expiry_date)->format('d M') }}</td>
                    <td style="text-align: right;">
                        @if($member->phone)
                        <button onclick="sendSMS('{{ $member->phone }}', '{{ $member->name }}', '{{ \Carbon\Carbon::parse($member->expiry_date)->format('d M') }}', 'expired')" 
                                class="btn-sms" 
                                style="background: #ff4d4d;"
                                title="Send SMS">
                            <i class="fas fa-sms"></i> SMS
                        </button>
                        @else
                        <span style="opacity: 0.3;" title="Phone missing"><i class="fas fa-sms"></i></span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; opacity: 0.5; padding: 2rem;">No recently expired</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- 4. Recent Payments (At the bottom) -->
<div class="card" style="margin-top: 2.5rem;">
    <h3 style="font-family: 'Oswald', sans-serif; text-transform: uppercase;">Recent Payments</h3>
    <div class="table-responsive">
        <table style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['recent_payments'] as $payment)
                <tr>
                    <td>{{ $payment->member->name }}</td>
                    <td>₹{{ number_format($payment->amount, 2) }}</td>
                    <td style="opacity: 0.7;">{{ $payment->payment_date }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; opacity: 0.5; padding: 3rem;">No recent payments</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function sendSMS(phone, name, expiryDate, status = 'expiring soon') {
    if (!phone) {
        alert("Phone number not available for this member.");
        return;
    }

    // Detect mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (!isMobile) {
        alert("Please use mobile to send SMS");
        return;
    }

    let message = `Hi ${name}, your gym membership is ${status}. Please renew soon.`;

    if (expiryDate) {
        message = `Hi ${name}, your gym membership expires on ${expiryDate}. Please renew soon.`;
        if (status === 'expired') {
            message = `Hi ${name}, your gym membership expired on ${expiryDate}. Please renew soon.`;
        }
    }

    const encodedMessage = encodeURIComponent(message);
    const url = `sms:+91${phone}?body=${encodedMessage}`;
    
    window.location.href = url;
}
</script>
@endpush
@endsection
