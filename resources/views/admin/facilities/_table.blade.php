@forelse($facilities as $facility)
<tr data-id="{{ $facility->id }}">
    <td class="hide-mobile">
        @php $ext = pathinfo($facility->image, PATHINFO_EXTENSION); @endphp
        @if(in_array(strtolower($ext), ['mp4', 'mov', 'MOV']))
            <div style="height: 48px; width: 48px; background: rgba(255,193,7,0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,193,7,0.2);">
                <i class="fas fa-play-circle" style="color: var(--gym-yellow);"></i>
            </div>
        @elseif($facility->image)
            <img src="{{ \App\Helpers\MediaHelper::getUrl($facility->image) }}" alt="{{ $facility->title }}" style="height: 48px; width: 48px; object-fit: cover; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.1);">
        @else
            <div style="height: 48px; width: 48px; background: rgba(255,255,255,0.05); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-image" style="opacity: 0.3;"></i>
            </div>
        @endif
    </td>
    <td style="font-weight: 600;">{{ $facility->title }}</td>
    <td>
        <div class="actions-stack" style="gap: 0.5rem;">
            <a href="{{ route('admin.facilities.edit', $facility->id) }}" 
               class="btn btn-ghost" style="padding: 0; width: 44px; height: 44px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; border-radius: 0.6rem; transition: all 0.2s;" title="Edit">
               <i class="fas fa-edit" style="color: #fff; font-size: 1rem;"></i>
            </a>
            <button type="button" 
                    class="btn btn-ghost btn-delete" 
                    data-id="{{ $facility->id }}" 
                    data-name="{{ $facility->title }}" 
                    style="padding: 0; width: 44px; height: 44px; border: 1px solid rgba(255,77,77,0.4); background: rgba(255,77,77,0.05); display: flex; align-items: center; justify-content: center; border-radius: 0.6rem; transition: all 0.2s;" title="Delete">
                <i class="fas fa-trash" style="color: #ff4d4d; font-size: 1rem;"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="3" style="text-align: center; opacity: 0.5; padding: 3rem;">No facilities found</td>
</tr>
@endforelse
