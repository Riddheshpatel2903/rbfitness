@extends('layouts.admin')

@section('title', 'Payment History')
@section('title_prefix', 'GYM')
@section('title_suffix', 'PAYMENTS')

@section('header_actions')
<a href="{{ route('admin.payments.create') }}" class="btn btn-primary">+ RECORD OFFLINE PAYMENT</a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th class="hide-mobile">ID</th>
                <th>Member</th>
                <th>Amount</th>
                <th>Payment Date</th>
                <th class="hide-mobile">New Expiry</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td class="hide-mobile">#{{ $payment->id }}</td>
                <td style="font-weight: 600;">{{ $payment->member->name }}</td>
                <td style="color: var(--gym-yellow);">₹{{ number_format($payment->amount, 2) }}</td>
                <td>{{ $payment->payment_date }}</td>
                <td class="hide-mobile" style="color: #4dff4d;">{{ $payment->expiry_date }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; opacity: 0.5; padding: 3rem;">No payments recorded</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 2rem;">
        {{ $payments->links() }}
    </div>
</div>
@endsection
