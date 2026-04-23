@extends('layouts.admin')

@section('title', 'Revenue & Expenses')
@section('title_prefix', 'GYM')
@section('title_suffix', 'REVENUE')

@section('header_actions')
<a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">+ ADD EXPENSE</a>
@endsection

@section('content')
{{-- Toast notification --}}
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

{{-- Revenue Stats Dashboard --}}
<div class="grid-stats" style="margin-bottom: 2.5rem; display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;">
    <div class="card" style="padding: 2rem; margin-bottom: 0; border-top: 4px solid #00ff88;">
        <p style="font-size: 0.75rem; opacity: 0.5; text-transform: uppercase; margin-bottom: 0.75rem; font-weight: 600; letter-spacing: 0.1em;">Total Collected (Fees)</p>
        <h2 id="revenue-fees" style="font-family: 'Oswald', sans-serif; font-size: 2.25rem; color: #00ff88;">₹{{ number_format($totalFees, 2) }}</h2>
    </div>
    <div class="card" style="padding: 2rem; margin-bottom: 0; border-top: 4px solid #ff4d4d;">
        <p style="font-size: 0.75rem; opacity: 0.5; text-transform: uppercase; margin-bottom: 0.75rem; font-weight: 600; letter-spacing: 0.1em;">Total Expenses</p>
        <h2 id="revenue-expenses" style="font-family: 'Oswald', sans-serif; font-size: 2.25rem; color: #ff4d4d;">₹{{ number_format($totalExpenses, 2) }}</h2>
    </div>
    <div class="card" style="padding: 2rem; margin-bottom: 0; border-top: 4px solid var(--gym-yellow);">
        <p style="font-size: 0.75rem; opacity: 0.5; text-transform: uppercase; margin-bottom: 0.75rem; font-weight: 600; letter-spacing: 0.1em;">Estimated Profit</p>
        <h2 id="revenue-profit" style="font-family: 'Oswald', sans-serif; font-size: 2.25rem; color: var(--gym-yellow);">₹{{ number_format($profit, 2) }}</h2>
    </div>
</div>

<div class="card">
    <div class="filter-container" style="margin-bottom: 2rem;">
        <div style="position:relative;flex:1;max-width:400px;">
            <input type="text" id="expense-search" placeholder="Search by title or category..." 
                   style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem 0.75rem 2.5rem; color: #fff;">
            <i class="fas fa-search" style="position:absolute; left: 1rem; top: 50%; transform: translateY(-50%); opacity: 0.3;"></i>
            <i class="fas fa-circle-notch fa-spin" id="search-spinner" style="position:absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--gym-yellow); display: none;"></i>
        </div>
        <div style="opacity: 0.5; font-size: 0.9rem;" id="total-count">
            {{ $expenses->total() }} recorded expenses
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Detail</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody id="expense-table-body" style="transition: opacity 0.2s;">
                @include('admin.expenses._table')
            </tbody>
        </table>
    </div>

    <div id="pagination-container" style="margin-top: 2rem; display: flex; justify-content: flex-end;">
        {{ $expenses->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    const searchInput = document.getElementById('expense-search');
    const tbody = document.getElementById('expense-table-body');
    const searchSpinner = document.getElementById('search-spinner');
    const paginationContainer = document.getElementById('pagination-container');
    const totalCount = document.getElementById('total-count');
    const toast = document.getElementById('ajax-toast');
    const toastMsg = document.getElementById('ajax-toast-msg');
    const toastIcon = document.getElementById('ajax-toast-icon');

    // Stats Elements
    const revenueFees = document.getElementById('revenue-fees');
    const revenueExpenses = document.getElementById('revenue-expenses');
    const revenueProfit = document.getElementById('revenue-profit');

    let searchTimer;
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

    function fetchExpenses(url = '{{ route("admin.expenses.index") }}') {
        tbody.style.opacity = '0.5';
        searchSpinner.style.display = 'block';

        const search = searchInput.value;
        const fetchUrl = new URL(url);
        if (search) fetchUrl.searchParams.set('search', search);

        fetch(fetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = data.rows;
            paginationContainer.innerHTML = data.pagination;
            totalCount.textContent = `${data.total} recorded expenses`;
            
            // Update Top Bar Stats
            if (data.stats) {
                revenueFees.textContent = '₹' + data.stats.fees;
                revenueExpenses.textContent = '₹' + data.stats.expenses;
                revenueProfit.textContent = '₹' + data.stats.profit;
            }

            tbody.style.opacity = '1';
            searchSpinner.style.display = 'none';
            bindPaginationLinks();
            bindDeleteButtons();
        })
        .catch(err => {
            console.error(err);
            searchSpinner.style.display = 'none';
            tbody.style.opacity = '1';
        });
    }

    function bindPaginationLinks() {
        paginationContainer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                fetchExpenses(link.href);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }

    function bindDeleteButtons() {
        document.querySelectorAll('.delete-expense').forEach(btn => {
            btn.onclick = function() {
                if (!confirm('Are you sure you want to remove this record?')) return;
                
                const id = this.dataset.id;
                const row = document.getElementById(`expense-row-${id}`);
                row.style.opacity = '0.3';

                fetch(`{{ url('rbadmin/expenses') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        row.remove();
                        showToast(data.message);
                        // Refresh stats after deletion
                        fetchExpenses();
                    } else {
                        row.style.opacity = '1';
                        showToast(data.message, 'error');
                    }
                });
            };
        });
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            fetchExpenses();
        }, 500);
    });

    bindPaginationLinks();
    bindDeleteButtons();
})();
</script>
@endpush
