@extends('layouts.admin')

@section('title', 'Manage Facilities')
@section('title_prefix', 'GYM')
@section('title_suffix', 'FACILITIES')

@section('header_actions')
<a href="{{ route('admin.facilities.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th class="hide-mobile">ID</th>
                <th>Media</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($facilities as $facility)
            <tr>
                <td class="hide-mobile">#{{ $facility->id }}</td>
                <td>
                    @if($facility->image)
                        @php $src = str_starts_with($facility->image, 'http') ? $facility->image : asset('storage/' . $facility->image); @endphp
                        @if(Str::endsWith($facility->image, ['.mp4', '.mov', '.MOV']) || str_contains($facility->image, 'video/upload'))
                            <video src="{{ $src }}" style="height: 40px; width: 60px; border-radius: 0.5rem; object-fit: cover;" muted></video>
                        @else
                            <img src="{{ $src }}" alt="{{ $facility->title }}" style="height: 40px; width: 60px; border-radius: 0.5rem; object-fit: cover;">
                        @endif
                    @else
                        <span style="opacity: 0.3;">No Media</span>
                    @endif
                </td>
                <td style="font-weight: 600;">{{ $facility->title }}</td>
                <td>
                    <div class="actions-stack">
                        <a href="{{ route('admin.facilities.edit', $facility->id) }}" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
                        <form action="{{ route('admin.facilities.destroy', $facility->id) }}" method="POST" onsubmit="return confirm('Remove this facility?')" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.1); width: 100%;">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; opacity: 0.5; padding: 3rem;">No facilities added to CMS</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
