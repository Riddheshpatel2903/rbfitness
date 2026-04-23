@forelse($plans as $plan)
<tr data-id="{{ $plan->id }}">
    <td class="hide-mobile">#{{ $plan->id }}</td>
    <td style="font-weight: 600;">{{ $plan->name }}</td>
    <td class="hide-mobile">{{ $plan->duration_days }} Days</td>
    <td class="hide-mobile">₹{{ number_format($plan->price, 2) }}</td>
    <td>
        <span class="status-badge {{ $plan->is_active ? 'bg-active' : 'bg-blocked' }}">
            {{ $plan->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td>
        <div class="actions-stack">
            <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
            <button type="button" class="btn btn-ghost btn-delete" data-id="{{ $plan->id }}" data-name="{{ $plan->name }}" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.1); width: 100%;">Delete</button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" style="text-align: center; opacity: 0.5; padding: 3rem;">No plans found</td>
</tr>
@endforelse
