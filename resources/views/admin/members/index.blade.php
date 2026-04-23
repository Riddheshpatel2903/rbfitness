@extends('layouts.admin')

@section('title', 'Members Management')
@section('title_prefix', 'GYM')
@section('title_suffix', 'MEMBERS')

@section('header_actions')
{{-- Wrap both buttons in a flex div so they sit side by side --}}
<div style="display:flex;gap:0.75rem;align-items:center;">
    <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Register
    </a>
    <button type="button" class="btn btn-ghost"
        onclick="document.getElementById('importModal').classList.add('open')"
        style="border:1px solid rgba(255,255,255,0.15);">
        <i class="fas fa-file-import"></i> Import CSV
    </button>
</div>
@endsection

@section('content')

{{-- Error flash (layout only handles success) --}}
@if(session('error'))
    <div style="background:rgba(255,77,77,0.12);border:1px solid rgba(255,77,77,0.3);border-radius:0.75rem;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff4d4d;font-size:0.9rem;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="filter-container" style="margin-bottom: 2rem;">
        <form action="{{ route('admin.members.index') }}" method="GET" class="filter-container">
            <input type="text" name="search" value="{{ request('search') }}"
                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: #fff;"
                placeholder="Search Name or Code...">
            <button type="submit" class="btn btn-ghost">Filter</button>
        </form>
    </div>

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
        <tbody>
            @forelse($members as $member)
            <tr>
                <td class="hide-mobile"><span style="font-family: monospace; opacity: 0.7;">{{ $member->member_code }}</span></td>
                <td style="font-weight: 600;">{{ $member->name }}</td>
                <td class="hide-mobile">{{ $member->plan?->name }}</td>
                <td style="color: {{ \Carbon\Carbon::parse($member->expiry_date)->isPast() ? '#ff4d4d' : '#fff' }}">{{ $member->expiry_date }}</td>
                <td class="hide-mobile"><span class="status-badge bg-{{ $member->status }}">{{ $member->status }}</span></td>
                <td class="hide-mobile">
                    @php
                        $isDue = ($member->status != 'active' || $member->payments->count() == 0) && $member->balance < 0;
                    @endphp

                    @if($member->balance < 0)
                        @if($isDue)
                            <span style="color: #ff4d4d; font-weight: 600; font-size: 0.9rem;">Due: ₹{{ number_format(abs($member->balance), 2) }}</span>
                        @else
                            <span style="opacity: 0.6; font-size: 0.9rem;">Remaining: ₹{{ number_format(abs($member->balance), 2) }}</span>
                        @endif
                    @elseif($member->balance > 0)
                        <span style="color: #00ff88; font-weight: 600; font-size: 0.9rem;">Advance: ₹{{ number_format($member->balance, 2) }}</span>
                    @else
                        <span style="opacity: 0.5; font-size: 0.9rem;">Paid</span>
                    @endif
                </td>
                <td>
                    <div class="actions-stack">
                        <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
                        <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Delete this member?')" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.1); width: 100%;">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; opacity: 0.5; padding: 3rem;">No members found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 2rem;">
        {{ $members->links() }}
    </div>
</div>

{{-- ============================================================
     Import CSV Modal
     ============================================================ --}}
<style>
    /* Hidden by default; .open class switches it to flex */
    #importModal { display: none; }
    #importModal.open { display: flex; }
</style>

<div id="importModal"
    style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);align-items:center;justify-content:center;">

    <div style="background:#1a1f2e;border:1px solid rgba(255,255,255,0.12);border-radius:1.25rem;padding:2rem;width:100%;max-width:480px;box-shadow:0 25px 60px rgba(0,0,0,0.5);margin:1rem;">

        {{-- Modal Header --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="margin:0;font-size:1.2rem;font-weight:700;">
                <i class="fas fa-file-import" style="color:#a78bfa;margin-right:0.5rem;"></i>
                Import Members from CSV
            </h3>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('open')"
                style="background:none;border:none;color:#fff;font-size:1.5rem;cursor:pointer;opacity:0.6;line-height:1;padding:0;">&times;</button>
        </div>

        {{-- One-click: Import from local file --}}
        <div style="background:rgba(0,255,136,0.06);border:1px solid rgba(0,255,136,0.2);border-radius:0.75rem;padding:1rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <div style="font-size:0.85rem;color:#a0e8c4;line-height:1.5;">
                <i class="fas fa-file-csv" style="color:#00ff88;margin-right:0.4rem;"></i>
                <strong style="color:#00ff88;">active_members.csv</strong> is ready on the server.<br>
                <span style="opacity:0.7;font-size:0.78rem;">Click to import directly — no upload needed.</span>
            </div>
            <form action="{{ route('admin.members.import-local') }}" method="POST" style="flex-shrink:0;">
                @csrf
                <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.2rem;font-size:0.85rem;white-space:nowrap;">
                    <i class="fas fa-bolt"></i> Import Now
                </button>
            </form>
        </div>

        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;opacity:0.4;">
            <div style="flex:1;height:1px;background:rgba(255,255,255,0.15);"></div>
            <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;">or upload a different file</span>
            <div style="flex:1;height:1px;background:rgba(255,255,255,0.15);"></div>
        </div>

        {{-- Info box --}}
        <div style="background:rgba(167,139,250,0.08);border:1px solid rgba(167,139,250,0.2);border-radius:0.75rem;padding:1rem;margin-bottom:1.5rem;font-size:0.82rem;color:#c4b5fd;line-height:1.6;">
            <i class="fas fa-info-circle" style="margin-right:0.4rem;"></i>
            <strong>Expected CSV columns:</strong><br>
            <code style="opacity:0.8;font-size:0.78rem;">Name, Phone, RenewalDate, PaymentDate, FeeAmount, GymGroup</code><br><br>
            <i class="fas fa-shield-alt" style="margin-right:0.4rem;"></i>
            Members already in the database will be <strong>skipped</strong> automatically.
        </div>

        {{-- Validation error --}}
        @if($errors->has('csv_file'))
            <div style="background:rgba(255,77,77,0.1);border:1px solid rgba(255,77,77,0.3);border-radius:0.75rem;padding:0.75rem 1rem;margin-bottom:1rem;color:#ff4d4d;font-size:0.85rem;">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first('csv_file') }}
            </div>
        @endif

        {{-- Upload form --}}
        <form action="{{ route('admin.members.import-csv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.85rem;opacity:0.7;margin-bottom:0.5rem;">Select CSV File</label>
                <input type="file" name="csv_file" accept=".csv,.txt" required
                    style="width:100%;padding:0.75rem 1rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.15);border-radius:0.75rem;color:#fff;font-size:0.9rem;box-sizing:border-box;cursor:pointer;">
            </div>

            <div style="display:flex;gap:0.75rem;">
                <button type="submit" class="btn btn-primary" style="flex:1;">
                    <i class="fas fa-upload"></i> Import Now
                </button>
                <button type="button" onclick="document.getElementById('importModal').classList.remove('open')"
                    class="btn btn-ghost" style="flex:1;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Re-open modal on validation error so user sees the error --}}
@if($errors->has('csv_file'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('importModal').classList.add('open');
    });
</script>
@endif

@endsection
