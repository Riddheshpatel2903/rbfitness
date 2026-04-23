@forelse($expenses as $expense)
    <tr id="expense-row-{{ $expense->id }}">
        <td>
            <div style="font-weight: 600;">{{ $expense->title }}</div>
            <div style="font-size: 0.75rem; opacity: 0.5;">{{ $expense->category }}</div>
        </td>
        <td style="color: #ff4d4d; font-weight: 700;">-₹{{ number_format($expense->amount, 2) }}</td>
        <td>{{ $expense->transaction_date->format('d M, Y') }}</td>
        <td>
            <span style="font-size: 0.8rem; opacity: 0.7;">{{ $expense->description ?: 'No details' }}</span>
            <br>
            <span style="font-size: 0.7rem; opacity: 0.4;">via {{ $expense->payment_method ?: 'N/A' }}</span>
        </td>
        <td class="actions-stack">
            <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="btn btn-ghost" style="padding: 0.5rem;"><i class="fas fa-edit"></i></a>
            <button type="button" class="btn btn-ghost delete-expense" data-id="{{ $expense->id }}" style="padding: 0.5rem; color: #ff4d4d;"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" style="text-align: center; padding: 4rem; opacity: 0.5;">
            <i class="fas fa-receipt" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
            No expenses recorded yet.
        </td>
    </tr>
@endforelse
