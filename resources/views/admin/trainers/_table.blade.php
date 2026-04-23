@forelse($trainers as $trainer)
<tr data-id="{{ $trainer->id }}">
    <td class="hide-mobile">
        @if($trainer->image)
            <img src="{{ \App\Helpers\MediaHelper::getUrl($trainer->image) }}" alt="{{ $trainer->name }}" style="height: 48px; width: 48px; object-fit: cover; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.1);">
        @else
            <div style="height: 48px; width: 48px; background: rgba(255,255,255,0.05); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-tie" style="opacity: 0.3;"></i>
            </div>
        @endif
    </td>
    <td style="font-weight: 600;">{{ $trainer->name }}</td>
    <td>{{ $trainer->specialization }}</td>
    <td>
        <div class="actions-stack" style="gap: 0.5rem;">
            <a href="{{ route('admin.trainers.edit', $trainer->id) }}" 
               class="btn btn-ghost" style="padding: 0; width: 44px; height: 44px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; border-radius: 0.6rem; transition: all 0.2s;" title="Edit">
               <i class="fas fa-edit" style="color: #fff; font-size: 1rem;"></i>
            </a>
            <button type="button" 
                    class="btn btn-ghost btn-delete" 
                    data-id="{{ $trainer->id }}" 
                    data-name="{{ $trainer->name }}" 
                    style="padding: 0; width: 44px; height: 44px; border: 1px solid rgba(255,77,77,0.4); background: rgba(255,77,77,0.05); display: flex; align-items: center; justify-content: center; border-radius: 0.6rem; transition: all 0.2s;" title="Delete">
                <i class="fas fa-trash" style="color: #ff4d4d; font-size: 1rem;"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" style="text-align: center; opacity: 0.5; padding: 3rem;">No trainers found</td>
</tr>
@endforelse
