@extends('layouts.admin')

@section('title', 'Manage Trainers')
@section('title_prefix', 'GYM')
@section('title_suffix', 'TRAINERS')

@section('header_actions')
<a href="{{ route('admin.trainers.create') }}" class="btn btn-primary">+ ADD NEW TRAINER</a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th class="hide-mobile">ID</th>
                <th>Photo</th>
                <th>Name</th>
                <th class="hide-mobile">Specialization</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trainers as $trainer)
            <tr>
                <td class="hide-mobile">#{{ $trainer->id }}</td>
                <td>
                    <img src="{{ $trainer->image ? asset('storage/' . $trainer->image) : asset('assets/TRAINER.JPEG') }}" alt="{{ $trainer->name }}" style="height: 40px; width: 40px; border-radius: 999px; object-fit: cover;">
                </td>
                <td style="font-weight: 600;">{{ $trainer->name }}</td>
                <td class="hide-mobile">{{ $trainer->specialization }}</td>
                <td>
                    <div class="actions-stack">
                        <a href="{{ route('admin.trainers.edit', $trainer->id) }}" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
                        <form action="{{ route('admin.trainers.destroy', $trainer->id) }}" method="POST" onsubmit="return confirm('Remove this trainer?')" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.1); width: 100%;">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; opacity: 0.5; padding: 3rem;">No trainers added to CMS</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
