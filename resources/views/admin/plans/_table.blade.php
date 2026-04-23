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
        <div class="actions-stack" style="gap: 0.5rem;">
            <a href="{{ route('admin.plans.edit', $plan->id) }}" 
               class="btn btn-ghost" style="padding: 0; width: 44px; height: 44px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; border-radius: 0.6rem; transition: all 0.2s;" title="Edit">
               <i class="fas fa-edit" style="color: #fff; font-size: 1rem;"></i>
            </a>
            <button type="button" 
                    class="btn btn-ghost btn-delete" 
                    data-id="{{ $plan->id }}" 
                    data-name="{{ $plan->name }}" 
                    style="padding: 0; width: 44px; height: 44px; border: 1px solid rgba(255,77,77,0.4); background: rgba(255,77,77,0.05); display: flex; align-items: center; justify-content: center; border-radius: 0.6rem; transition: all 0.2s;" title="Delete">
                <i class="fas fa-trash" style="color: #ff4d4d; font-size: 1rem;"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" style="text-align: center; opacity: 0.5; padding: 3rem;">No plans found</td>
</tr>
@endforelse
