@forelse($facilities as $facility)
<tr data-id="{{ $facility->id }}">
    <td class="hide-mobile">
        @php $ext = pathinfo($facility->image, PATHINFO_EXTENSION); @endphp
        @if(in_array(strtolower($ext), ['mp4', 'mov']))
            <div style="height: 40px; width: 40px; background: rgba(255,255,255,0.05); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-video" style="opacity: 0.3;"></i>
            </div>
        @elseif($facility->image)
            <img src="{{ asset($facility->image) }}" alt="{{ $facility->title }}" style="height: 40px; width: 40px; object-fit: cover; border-radius: 0.5rem;">
        @else
            <div style="height: 40px; width: 40px; background: rgba(255,255,255,0.05); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-image" style="opacity: 0.3;"></i>
            </div>
        @endif
    </td>
    <td style="font-weight: 600;">{{ $facility->title }}</td>
    <td>
        <div class="actions-stack">
            <a href="{{ route('admin.facilities.edit', $facility->id) }}" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
            <button type="button" class="btn btn-ghost btn-delete" data-id="{{ $facility->id }}" data-name="{{ $facility->title }}" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.1); width: 100%;">Delete</button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="3" style="text-align: center; opacity: 0.5; padding: 3rem;">No facilities found</td>
</tr>
@endforelse
