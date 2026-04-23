@extends('layouts.admin')

@section('title', 'Facilities Management')
@section('title_prefix', 'GYM')
@section('title_suffix', 'FACILITIES')

@section('header_actions')
<a href="{{ route('admin.facilities.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Facility</a>
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
                placeholder="Search facilities..."
                autocomplete="off"
                style="width:100%;padding:0.75rem 1rem 0.75rem 2.75rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:0.75rem;color:#fff;box-sizing:border-box;">
        </div>
        <div id="search-spinner" style="display:none;opacity:0.5;font-size:0.8rem;"><i class="fas fa-circle-notch fa-spin"></i> Searching…</div>
        <span id="total-count" style="opacity:0.45;font-size:0.82rem;white-space:nowrap;">{{ $facilities->count() }} total facilities</span>
    </div>

    <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th class="hide-mobile">Media</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @include('admin.facilities._table', ['facilities' => $facilities])
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const BASE_URL = '{{ route("admin.facilities.index") }}';
    const DELETE_BASE = '{{ url("rbadmin/facilities") }}';
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

    function fetchFacilities(search = '') {
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
            totalCount.textContent = `${data.total} total facilities`;
            tbody.style.opacity = '1';
            searchSpinner.style.display = 'none';
            bindDeleteEvents();
        })
        .catch(err => {
            console.error(err);
            searchSpinner.style.display = 'none';
            tbody.style.opacity = '1';
            showToast('Error loading facilities.', 'error');
        });
    }

    let searchTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            fetchFacilities(searchInput.value.trim());
        }, 300);
    });

    function bindDeleteEvents() {
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const row = this.closest('tr');

                if (!confirm(`Are you sure you want to remove facility "${name}"?`)) return;

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
                        fetchFacilities(searchInput.value.trim());
                    } else {
                        row.style.opacity = '1';
                        row.style.pointerEvents = 'all';
                        showToast(data.message || 'Error removing facility.', 'error');
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

    bindDeleteEvents();
})();
</script>
@endpush
