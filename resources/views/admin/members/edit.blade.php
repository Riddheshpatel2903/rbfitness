@extends('layouts.admin')

@section('title', 'Edit Member')
@section('title_prefix', 'GYM')
@section('title_suffix', 'EDIT MEMBER')

@section('header_actions')
<a href="{{ route('admin.members.index') }}" class="btn btn-ghost">← Back</a>
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

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form id="edit-form" action="{{ route('admin.members.update', $member->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $member->name) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. John Doe" required>
                <p class="error-msg" id="error-name" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Member Code (ID)</label>
                <input type="text" name="member_code" value="{{ old('member_code', $member->member_code) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. RB0001" required>
                <p class="error-msg" id="error-member_code" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $member->phone) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. 919876543210" required>
                <p class="error-msg" id="error-phone" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $member->email) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. john@example.com">
                <p class="error-msg" id="error-email" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Membership Plan</label>
                <select name="plan_id" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ old('plan_id', $member->plan_id) == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }} ({{ $plan->duration_days }} Days)
                        </option>
                    @endforeach
                </select>
                <p class="error-msg" id="error-plan_id" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Status</label>
                <select name="status" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                    <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ old('status', $member->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="blocked" {{ old('status', $member->status) == 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
                <p class="error-msg" id="error-status" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Join Date</label>
                <input type="date" name="join_date" value="{{ old('join_date', $member->join_date) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <p class="error-msg" id="error-join_date" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Expiry Date</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date', $member->expiry_date) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <p class="error-msg" id="error-expiry_date" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
        </div>

        <button type="submit" id="submit-btn" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            <span class="btn-text">UPDATE MEMBER DETAILS</span>
            <i class="fas fa-circle-notch fa-spin btn-spinner" style="display:none;"></i>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    const form = document.getElementById('edit-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnSpinner = submitBtn.querySelector('.btn-spinner');
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

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Reset errors
        document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');
        
        // Loading state
        submitBtn.disabled = true;
        btnText.style.opacity = '0.5';
        btnSpinner.style.display = 'inline-block';

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.members.index") }}';
                }, 1000);
            } else {
                if (data.errors) {
                    Object.entries(data.errors).forEach(([key, msgs]) => {
                        const errEl = document.getElementById(`error-${key}`);
                        if (errEl) {
                            errEl.textContent = msgs[0];
                            errEl.style.display = 'block';
                        }
                    });
                    showToast('Please correct the errors.', 'error');
                } else {
                    showToast(data.message || 'Something went wrong.', 'error');
                }
            }
        } catch (err) {
            console.error(err);
            showToast('Network error.', 'error');
        } finally {
            submitBtn.disabled = false;
            btnText.style.opacity = '1';
            btnSpinner.style.display = 'none';
        }
    });
})();
</script>
@endpush
