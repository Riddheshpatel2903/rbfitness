@extends('layouts.admin')

@section('title', 'Plan Categories')
@section('title_prefix', 'GYM')
@section('title_suffix', 'CATEGORIES')

@section('header_actions')
<a href="{{ route('admin.plan_categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a>
@endsection

@section('content')
{{-- Toast notification (AJAX feedback) --}}
<div id="ajax-toast" style="
    position:fixed;bottom:2rem;right:2rem;z-index:99999;
    background:#1a1f2e;border:1px solid rgba(255,255,255,0.12);
    border-radius:0.875rem;padding:1rem 1.5rem;
    display:flex;align-items:center;gap:0.75rem;
    box-shadow:0 20px 40px rgba(0,0,0,0.5);
    transform:translateY(120%);opacity:0;
    transition:transform 0.35s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease;
    max-width:360px;">
    <span id="ajax-toast-icon" style="font-size:1.2rem;"></span>
    <span id="ajax-toast-msg" style="font-size:0.9rem;font-weight:500;"></span>
</div>

<div class="card">
    <div class="filter-container" style="margin-bottom: 2rem;">
        <div style="position:relative;flex:1;max-width:400px;">
            <i class="fas fa-search" style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);opacity:0.4;pointer-events:none;"></i>
            <input id="search-input" type="text" 
                placeholder="Search categories..."
                autocomplete="off"
                style="width:100%;padding:0.75rem 1rem 0.75rem 2.75rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:0.75rem;color:#fff;box-sizing:border-box;">
        </div>
        <div id="search-spinner" style="display:none;opacity:0.5;font-size:0.8rem;"><i class="fas fa-circle-notch fa-spin"></i> Searching…</div>
        <span id="total-count" style="opacity:0.45;font-size:0.82rem;white-space:nowrap;">{{ $categories->count() }} total categories</span>
    </div>

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
        <tbody id="table-body">
            @include('admin.plan_categories._table', ['categories' => $categories])
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

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const BASE_URL = '{{ route("admin.plan_categories.index") }}';
    const DELETE_BASE = '{{ url("rbadmin/plan_categories") }}';
    const CSRF = '{{ csrf_token() }}';

    const searchInput = document.getElementById('search-input');
    const tbody = document.getElementById('table-body');
    const totalCount = document.getElementById('total-count');
    const searchSpinner = document.getElementById('search-spinner');
    const toast = document.getElementById('ajax-toast');
    const toastMsg = document.getElementById('ajax-toast-msg');
    const toastIcon = document.getElementById('ajax-toast-icon');

    let toastTimer;
    function showToast(msg, type = 'success') {
        toastIcon.innerHTML = type === 'success'
            ? '<i class="fas fa-check-circle" style="color:#00ff88;"></i>'
            : '<i class="fas fa-times-circle" style="color:#ff4d4d;"></i>';
        toastMsg.textContent = msg;
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.style.transform = 'translateY(120%)';
            toast.style.opacity = '0';
        }, 3500);
    }

    function fetchCategories(search = '') {
        const url = new URL(BASE_URL);
        if (search) url.searchParams.set('search', search);

        searchSpinner.style.display = 'inline-flex';
        tbody.style.opacity = '0.4';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            tbody.innerHTML = data.rows;
            totalCount.textContent = `${data.total} total categories`;
            tbody.style.opacity = '1';
            searchSpinner.style.display = 'none';
            bindEvents();
        })
        .catch(err => {
            console.error(err);
            searchSpinner.style.display = 'none';
            tbody.style.opacity = '1';
            showToast('Error loading categories.', 'error');
        });
    }

    let searchTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            fetchCategories(searchInput.value.trim());
        }, 300);
    });

    function bindEvents() {
        // Toggle status
        document.querySelectorAll('.toggle-status').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const id = this.dataset.id;
                const isActive = this.checked;
                
                fetch(`/rbadmin/plan_categories/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                    } else {
                        this.checked = !isActive;
                        showToast('Error updating status.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.checked = !isActive;
                    showToast('Network error.', 'error');
                });
            });
        });

        // Delete
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const row = this.closest('tr');

                if (!confirm(`Are you sure you want to delete category "${name}"?`)) return;

                row.style.opacity = '0.3';
                row.style.pointerEvents = 'none';

                fetch(`${DELETE_BASE}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        row.remove();
                        showToast(data.message);
                        fetchCategories(searchInput.value.trim());
                    } else {
                        row.style.opacity = '1';
                        row.style.pointerEvents = 'all';
                        showToast(data.message || 'Error deleting category.', 'error');
                    }
                })
                .catch(err => {
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'all';
                    showToast('Network error.', 'error');
                });
            });
        });
    }

    bindEvents();
})();
</script>
@endpush
