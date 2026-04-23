@forelse($categories as $category)
<tr data-id="{{ $category->id }}">
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
            <button type="button" class="btn btn-ghost btn-delete" data-id="{{ $category->id }}" data-name="{{ $category->name }}" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.1); width: 100%;">Delete</button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" style="text-align: center; opacity: 0.5; padding: 3rem;">No categories found</td>
</tr>
@endforelse
