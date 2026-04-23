@forelse($payments as $payment)
<tr>
    <td>{{ $payment->payment_date }}</td>
    <td style="font-weight: 600;">{{ $payment->member->name }}</td>
    <td class="hide-mobile">{{ $payment->plan?->name }}</td>
    <td style="font-weight: 700; color: #00ff88;">₹{{ number_format($payment->amount, 2) }}</td>
    <td class="hide-mobile">
        <span class="status-badge bg-active">Paid</span>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" style="text-align: center; opacity: 0.5; padding: 3rem;">No payments found</td>
</tr>
@endforelse
