@extends('layouts.admin')

@section('title', 'Members Management')
@section('title_prefix', 'GYM')
@section('title_suffix', 'MEMBERS')

@section('header_actions')
<div style="display:flex;gap:0.75rem;align-items:center;">
    <a href="{{ route('admin.members.export') }}" class="btn btn-ghost" style="border:1px solid rgba(255,255,255,0.15); padding:0.6rem 0.8rem; font-size:0.85rem;">
        <i class="fas fa-file-export"></i> Export
    </a>
    <button type="button" class="btn btn-ghost"
        onclick="document.getElementById('importModal').classList.add('open')"
        style="border:1px solid rgba(255,255,255,0.15); padding:0.6rem 0.8rem; font-size:0.85rem;">
        <i class="fas fa-file-import"></i> Import
    </button>
    <a href="{{ route('admin.members.create') }}" class="btn btn-primary" style="padding:0.65rem 1.25rem;">
        <i class="fas fa-plus"></i> Register
    </a>
</div>
@endsection

@section('content')

{{-- Error flash --}}
@if(session('error'))
    <div style="background:rgba(255,77,77,0.12);border:1px solid rgba(255,77,77,0.3);border-radius:0.75rem;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff4d4d;font-size:0.9rem;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

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
    {{-- Search & Filter bar --}}
    <div class="filter-container" style="margin-bottom:2rem; display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
        <div style="position:relative;flex:1;min-width:300px;">
            <i class="fas fa-search" style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);opacity:0.4;pointer-events:none;"></i>
            <input id="member-search" type="text"
                value="{{ request('search') }}"
                placeholder="Search name, code, or phone..."
                autocomplete="off"
                style="width:100%;padding:0.75rem 1rem 0.75rem 2.75rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:0.75rem;color:#fff;box-sizing:border-box;">
        </div>

        <div style="min-width:200px;">
            <select id="status-filter" style="width:100%;padding:0.75rem 1rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:0.75rem;color:#fff;box-sizing:border-box;cursor:pointer;">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
            </select>
        </div>

        <div id="search-spinner" style="display:none;opacity:0.5;font-size:0.8rem;"><i class="fas fa-circle-notch fa-spin"></i> Searching…</div>
        <span id="member-count" style="opacity:0.45;font-size:0.82rem;white-space:nowrap;margin-left:auto;">{{ $members->total() }} members</span>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="hide-mobile">Code</th>
                    <th>Name</th>
                    <th class="hide-mobile">Plan</th>
                    <th>Expiry</th>
                    <th class="hide-mobile">Status</th>
                    <th class="hide-mobile">Fees Balance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="members-tbody">
                @include('admin.members._table', ['members' => $members])
            </tbody>
        </table>
    </div>

    {{-- Loading overlay --}}
    <div id="table-loading" style="display:none;text-align:center;padding:3rem;opacity:0.5;">
        <i class="fas fa-circle-notch fa-spin" style="font-size:1.5rem;"></i>
        <p style="margin-top:0.75rem;font-size:0.85rem;">Loading members…</p>
    </div>

    {{-- Pagination --}}
    <div id="members-pagination" style="margin-top:2rem;">
        {{ $members->links() }}
    </div>
</div>

{{-- ================================================================
     Import CSV Modal
     ================================================================ --}}
<style>
    #importModal { display:none; }
    #importModal.open { display:flex; }
</style>

<div id="importModal"
    style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#1a1f2e;border:1px solid rgba(255,255,255,0.12);border-radius:1.25rem;padding:2rem;width:100%;max-width:480px;box-shadow:0 25px 60px rgba(0,0,0,0.5);margin:1rem;">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="margin:0;font-size:1.2rem;font-weight:700;">
                <i class="fas fa-file-import" style="color:#a78bfa;margin-right:0.5rem;"></i>
                Import Members from CSV
            </h3>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('open')"
                style="background:none;border:none;color:#fff;font-size:1.5rem;cursor:pointer;opacity:0.6;line-height:1;padding:0;">&times;</button>
        </div>

        {{-- One-click local file --}}
        <div style="background:rgba(0,255,136,0.06);border:1px solid rgba(0,255,136,0.2);border-radius:0.75rem;padding:1rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <div style="font-size:0.85rem;color:#a0e8c4;line-height:1.5;">
                <i class="fas fa-file-csv" style="color:#00ff88;margin-right:0.4rem;"></i>
                <strong style="color:#00ff88;">active_members.csv</strong> is ready on the server.<br>
                <span style="opacity:0.7;font-size:0.78rem;">Click to import directly — no upload needed.</span>
            </div>
            <form id="import-local-form" action="{{ route('admin.members.import-local') }}" method="POST" style="flex-shrink:0;">
                @csrf
                <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.2rem;font-size:0.85rem;white-space:nowrap;">
                    <i class="fas fa-bolt import-icon"></i> 
                    <span class="import-text">Import Now</span>
                    <i class="fas fa-circle-notch fa-spin import-spinner" style="display:none;"></i>
                </button>
            </form>
        </div>

        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;opacity:0.4;">
            <div style="flex:1;height:1px;background:rgba(255,255,255,0.15);"></div>
            <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;">or upload a different file</span>
            <div style="flex:1;height:1px;background:rgba(255,255,255,0.15);"></div>
        </div>

        <div style="background:rgba(167,139,250,0.08);border:1px solid rgba(167,139,250,0.2);border-radius:0.75rem;padding:1rem;margin-bottom:1.5rem;font-size:0.82rem;color:#c4b5fd;line-height:1.6;">
            <i class="fas fa-info-circle" style="margin-right:0.4rem;"></i>
            <strong>Expected CSV columns:</strong><br>
            <code style="opacity:0.8;font-size:0.78rem;">Name, Phone, RenewalDate, PaymentDate, FeeAmount, GymGroup</code><br><br>
            <i class="fas fa-shield-alt" style="margin-right:0.4rem;"></i>
            Members already in the database will be <strong>skipped</strong> automatically.
        </div>

        @if($errors->has('csv_file'))
            <div style="background:rgba(255,77,77,0.1);border:1px solid rgba(255,77,77,0.3);border-radius:0.75rem;padding:0.75rem 1rem;margin-bottom:1rem;color:#ff4d4d;font-size:0.85rem;">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first('csv_file') }}
            </div>
        @endif

        <form id="import-upload-form" action="{{ route('admin.members.import-csv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.85rem;opacity:0.7;margin-bottom:0.5rem;">Select CSV File</label>
                <input type="file" name="csv_file" accept=".csv,.txt" required
                    style="width:100%;padding:0.75rem 1rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.15);border-radius:0.75rem;color:#fff;font-size:0.9rem;box-sizing:border-box;cursor:pointer;">
            </div>
            <div style="display:flex;gap:0.75rem;">
                <button type="submit" class="btn btn-primary" style="flex:1;">
                    <i class="fas fa-upload import-icon"></i> 
                    <span class="import-text">Upload & Import</span>
                    <i class="fas fa-circle-notch fa-spin import-spinner" style="display:none;"></i>
                </button>
                <button type="button" onclick="document.getElementById('importModal').classList.remove('open')"
                    class="btn btn-ghost" style="flex:1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

@if($errors->has('csv_file'))
<script>document.addEventListener('DOMContentLoaded',()=>document.getElementById('importModal').classList.add('open'));</script>
@endif

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ─── Config ────────────────────────────────────────────────────────────────
    const BASE_URL  = '{{ route("admin.members.index") }}';
    const DELETE_BASE = '{{ url("rbadmin/members") }}';
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    // ─── Elements ──────────────────────────────────────────────────────────────
    const searchInput  = document.getElementById('member-search');
    const statusFilter = document.getElementById('status-filter');
    const tbody        = document.getElementById('members-tbody');
    const pagination   = document.getElementById('members-pagination');
    const countBadge   = document.getElementById('member-count');
    const spinner      = document.getElementById('search-spinner');
    const tableLoading = document.getElementById('table-loading');
    const toast        = document.getElementById('ajax-toast');
    const toastMsg     = document.getElementById('ajax-toast-msg');
    const toastIcon    = document.getElementById('ajax-toast-icon');

    // ─── Toast ─────────────────────────────────────────────────────────────────
    let toastTimer;
    function showToast(msg, type = 'success') {
        toastIcon.innerHTML = type === 'success'
            ? '<i class="fas fa-check-circle" style="color:#00ff88;"></i>'
            : '<i class="fas fa-times-circle" style="color:#ff4d4d;"></i>';
        toastMsg.textContent = msg;
        toast.style.transform = 'translateY(0)';
        toast.style.opacity   = '1';
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.style.transform = 'translateY(120%)';
            toast.style.opacity   = '0';
        }, 3500);
    }

    // ─── AJAX Fetch Members ────────────────────────────────────────────────────
    let currentRequest = null;

    function fetchMembers(params = {}) {
        if (currentRequest) currentRequest.abort();

        const url = new URL(BASE_URL);
        Object.entries(params).forEach(([k, v]) => { if (v) url.searchParams.set(k, v); });

        // Show loading state
        spinner.style.display = 'inline-flex';
        tbody.style.opacity   = '0.4';
        tbody.style.pointerEvents = 'none';

        const controller = new AbortController();
        currentRequest = controller;

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            signal: controller.signal,
        })
        .then(r => r.json())
        .then(data => {
            tbody.innerHTML       = data.rows;
            pagination.innerHTML  = data.pagination;
            countBadge.textContent = data.total + ' members';
            tbody.style.opacity   = '1';
            tbody.style.pointerEvents = '';
            spinner.style.display = 'none';
            bindDeleteButtons();
            bindPaginationLinks();
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                showToast('Failed to load members.', 'error');
                tbody.style.opacity = '1';
                tbody.style.pointerEvents = '';
                spinner.style.display = 'none';
            }
        });
    }

    // ─── Debounced Search ──────────────────────────────────────────────────────
    let searchTimer;
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchMembers({ 
                    search: searchInput.value.trim(),
                    status: statusFilter.value
                });
                
                const url = new URL(window.location);
                if (searchInput.value.trim()) url.searchParams.set('search', searchInput.value.trim());
                else url.searchParams.delete('search');
                
                url.searchParams.delete('page');
                window.history.replaceState({}, '', url);
            }, 350);
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            fetchMembers({ 
                search: searchInput.value.trim(),
                status: statusFilter.value
            });
            
            const url = new URL(window.location);
            if (statusFilter.value) url.searchParams.set('status', statusFilter.value);
            else url.searchParams.delete('status');
            
            url.searchParams.delete('page');
            window.history.replaceState({}, '', url);
        });
    }

    // ─── AJAX Pagination ───────────────────────────────────────────────────────
    function bindPaginationLinks() {
        pagination.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const url = new URL(link.href);
                const page   = url.searchParams.get('page') || 1;
                const search = searchInput?.value.trim() || '';
                const status = statusFilter?.value || '';
                fetchMembers({ page, search, status });
                window.history.pushState({}, '', link.href);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }
    bindPaginationLinks();

    // ─── AJAX Delete ───────────────────────────────────────────────────────────
    function deleteMember(id, name, row) {
        if (!confirm(`Delete member "${name}"? This cannot be undone.`)) return;

        // Optimistic UI: fade row
        row.style.transition  = 'opacity 0.3s, transform 0.3s';
        row.style.opacity     = '0.3';
        row.style.pointerEvents = 'none';

        fetch(`${DELETE_BASE}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Animate row out
                row.style.transform  = 'translateX(40px)';
                row.style.opacity    = '0';
                setTimeout(() => {
                    row.remove();
                    // Update count
                    const cur = parseInt(countBadge.textContent) || 1;
                    countBadge.textContent = Math.max(0, cur - 1) + ' members';
                    // If table is now empty, show empty state
                    if (!tbody.querySelector('tr[data-member-id]')) {
                        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;opacity:0.5;padding:3rem;">No members found</td></tr>`;
                    }
                }, 300);
                showToast(`"${name}" deleted successfully.`, 'success');
            } else {
                row.style.opacity = '1';
                row.style.pointerEvents = '';
                showToast('Failed to delete member.', 'error');
            }
        })
        .catch(() => {
            row.style.opacity = '1';
            row.style.pointerEvents = '';
            showToast('Network error. Please try again.', 'error');
        });
    }

    function bindDeleteButtons() {
        tbody.querySelectorAll('.btn-delete-member').forEach(btn => {
            btn.addEventListener('click', function () {
                const id   = this.dataset.id;
                const name = this.dataset.name;
                const row  = this.closest('tr');
                deleteMember(id, name, row);
            });
        });
    }
    bindDeleteButtons();

    // ─── AJAX CSV Imports ──────────────────────────────────────────────────────
    function handleImportForm(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', e => {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const icon = btn.querySelector('.import-icon');
            const text = btn.querySelector('.import-text');
            const spinner = btn.querySelector('.import-spinner');

            // Disable UI
            btn.disabled = true;
            if (icon) icon.style.display = 'none';
            if (text) text.style.opacity = '0.5';
            if (spinner) spinner.style.display = 'inline-block';

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    document.getElementById('importModal').classList.remove('open');
                    fetchMembers({ page: 1, search: searchInput?.value.trim(), status: statusFilter?.value });
                } else {
                    showToast(data.message || 'Import failed.', 'error');
                }
            })
            .catch(() => showToast('Network error during import.', 'error'))
            .finally(() => {
                btn.disabled = false;
                if (icon) icon.style.display = 'inline-block';
                if (text) text.style.opacity = '1';
                if (spinner) spinner.style.display = 'none';
                form.reset();
            });
        });
    }

    handleImportForm('import-local-form');
    handleImportForm('import-upload-form');

    // ─── Browser back/forward ──────────────────────────────────────────────────
    window.addEventListener('popstate', () => {
        const url    = new URL(window.location);
        const page   = url.searchParams.get('page') || 1;
        const search = url.searchParams.get('search') || '';
        const status = url.searchParams.get('status') || '';
        if (searchInput) searchInput.value = search;
        if (statusFilter) statusFilter.value = status;
        fetchMembers({ page, search, status });
    });

})();
</script>
@endpush
