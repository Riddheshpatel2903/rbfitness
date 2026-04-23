@forelse($trainers as $trainer)
<tr data-id="{{ $trainer->id }}">
    <td class="hide-mobile">
        @if($trainer->image)
            <img src="{{ asset($trainer->image) }}" alt="{{ $trainer->name }}" style="height: 40px; width: 40px; object-fit: cover; border-radius: 0.5rem;">
        @else
            <div style="height: 40px; width: 40px; background: rgba(255,255,255,0.05); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user" style="opacity: 0.3;"></i>
            </div>
        @endif
    </td>
    <td style="font-weight: 600;">{{ $trainer->name }}</td>
    <td>{{ $trainer->specialization }}</td>
    <td>
        <div class="actions-stack">
            <a href="{{ route('admin.trainers.edit', $trainer->id) }}" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
            <button type="button" class="btn btn-ghost btn-delete" data-id="{{ $trainer->id }}" data-name="{{ $trainer->name }}" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.1); width: 100%;">Delete</button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" style="text-align: center; opacity: 0.5; padding: 3rem;">No trainers found</td>
</tr>
@endforelse
