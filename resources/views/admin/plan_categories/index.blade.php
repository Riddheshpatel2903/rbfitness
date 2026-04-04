@extends('layouts.admin')

@section('title', 'Plan Categories')
@section('title_prefix', 'GYM')
@section('title_suffix', 'CATEGORIES')

@section('header_actions')
<a href="{{ route('admin.plan_categories.create') }}" class="btn btn-primary">+ ADD CATEGORY</a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th class="hide-mobile">ID</th>
                <th>Name</th>
                <th class="hide-mobile">Slug</th>
                <th class="hide-mobile">Plans Count</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td class="hide-mobile">#{{ $category->id }}</td>
                <td style="font-weight: 600;">{{ $category->name }}</td>
                <td class="hide-mobile">{{ $category->slug }}</td>
                <td class="hide-mobile">{{ $category->plans_count }}</td>
                <td>
                    <label class="switch">
                        <input type="checkbox" class="toggle-status" data-id="{{ $category->id }}" {{ $category->is_active ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </td>
                <td>
                    <div class="actions-stack">
                        <a href="{{ route('admin.plan_categories.edit', $category->id) }}" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
                        <form action="{{ route('admin.plan_categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?')" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.1); width: 100%;">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; opacity: 0.5; padding: 3rem;">No categories found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
/* Modern Toggle Switch */
.switch {
  position: relative;
  display: inline-block;
  width: 46px;
  height: 24px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255,255,255,0.1);
  transition: .4s;
  border-radius: 24px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: var(--gym-yellow);
}

input:focus + .slider {
  box-shadow: 0 0 1px var(--gym-yellow);
}

input:checked + .slider:before {
  transform: translateX(22px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.toggle-status');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.dataset.id;
            const isActive = this.checked;
            
            fetch(`/rbadmin/plan_categories/${id}/toggle`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Optional: Show a subtle toast or message
                    console.log(data.message);
                } else {
                    // Revert if error
                    this.checked = !isActive;
                    alert('Error updating status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !isActive;
                alert('Network error. Please try again.');
            });
        });
    });
});
</script>
@endsection
