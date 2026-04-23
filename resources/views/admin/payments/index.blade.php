@extends('layouts.admin')

@section('title', 'Payment History')
@section('title_prefix', 'PAYMENT')
@section('title_suffix', 'LOGS')

@section('header_actions')
<a href="{{ route('admin.payments.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Payment</a>
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
                placeholder="Search member name or code..."
                autocomplete="off"
                style="width:100%;padding:0.75rem 1rem 0.75rem 2.75rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:0.75rem;color:#fff;box-sizing:border-box;">
        </div>
        <div id="search-spinner" style="display:none;opacity:0.5;font-size:0.8rem;"><i class="fas fa-circle-notch fa-spin"></i> Searching…</div>
        <span id="total-count" style="opacity:0.45;font-size:0.82rem;white-space:nowrap;">{{ $payments->total() }} total payments</span>
    </div>

    <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Member</th>
                <th class="hide-mobile">Plan Used</th>
                <th>Amount</th>
                <th class="hide-mobile">Status</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @include('admin.payments._table', ['payments' => $payments])
        </tbody>
    </table>
</div>

<div id="pagination-container" style="margin-top:2rem;">
    {{ $payments->links() }}
</div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const BASE_URL = '{{ route("admin.payments.index") }}';
    const searchInput = document.getElementById('search-input');
    const tbody = document.getElementById('table-body');
    const paginationContainer = document.getElementById('pagination-container');
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

    function fetchPayments(params = {}) {
        const url = new URL(BASE_URL);
        Object.entries(params).forEach(([k, v]) => { if (v) url.searchParams.set(k, v); });

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
            paginationContainer.innerHTML = data.pagination;
            totalCount.textContent = `${data.total} total payments`;
            tbody.style.opacity = '1';
            searchSpinner.style.display = 'none';
            bindPaginationLinks();
        })
        .catch(err => {
            console.error(err);
            searchSpinner.style.display = 'none';
            tbody.style.opacity = '1';
            showToast('Error loading payments.', 'error');
        });
    }

    let searchTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            fetchPayments({ search: searchInput.value.trim() });
        }, 300);
    });

    function bindPaginationLinks() {
        paginationContainer.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const url = new URL(link.href);
                const page = url.searchParams.get('page') || 1;
                fetchPayments({ page, search: searchInput.value.trim() });
                window.history.pushState({}, '', link.href);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }

    bindPaginationLinks();

    window.addEventListener('popstate', () => {
        const url = new URL(window.location);
        const page = url.searchParams.get('page') || 1;
        const search = url.searchParams.get('search') || '';
        if (searchInput) searchInput.value = search;
        fetchPayments({ page, search });
    });

})();
</script>
@endpush
