@foreach($members as $member)
<tr data-member-id="{{ $member->id }}">
    <td class="hide-mobile">
        <span style="font-family: monospace; opacity: 0.7;">{{ $member->member_code }}</span>
    </td>
    <td style="font-weight: 600;">{{ $member->name }}</td>
    <td class="hide-mobile">{{ $member->plan?->name }}</td>
    <td style="color: {{ \Carbon\Carbon::parse($member->expiry_date)->isPast() ? '#ff4d4d' : '#fff' }}">
        {{ $member->expiry_date }}
    </td>
    <td class="hide-mobile">
        <span class="status-badge bg-{{ $member->status }}">{{ $member->status }}</span>
    </td>
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
        <div class="actions-stack" style="gap: 0.5rem;">
            <a href="{{ route('admin.members.edit', $member->id) }}" 
               class="btn btn-ghost" style="padding: 0; width: 44px; height: 44px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; border-radius: 0.6rem; transition: all 0.2s;" title="Edit">
               <i class="fas fa-edit" style="color: #fff; font-size: 1rem;"></i>
            </a>
            <button type="button" 
                class="btn btn-ghost btn-delete-member" 
                data-id="{{ $member->id }}" 
                data-name="{{ $member->name }}" 
                style="padding: 0; width: 44px; height: 44px; border: 1px solid rgba(255,77,77,0.4); background: rgba(255,77,77,0.05); display: flex; align-items: center; justify-content: center; border-radius: 0.6rem; transition: all 0.2s;" title="Delete">
                <i class="fas fa-trash" style="color: #ff4d4d; font-size: 1rem;"></i>
            </button>
        </div>
    </td>
</tr>
@endforeach

@if($members->isEmpty())
<tr id="no-results-row">
    <td colspan="7" style="text-align: center; opacity: 0.5; padding: 3rem;">No members found</td>
</tr>
@endif
