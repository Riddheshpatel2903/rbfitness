@extends('layouts.admin')

@section('title', 'Register Member')
@section('title_prefix', 'GYM')
@section('title_suffix', 'REGISTRATION')

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
    <form id="register-form" action="{{ route('admin.members.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. John Doe" required>
                <p class="error-msg" id="error-name" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Member Code (Auto)</label>
                <input type="text" name="member_code" value="{{ old('member_code', $nextCode) }}" style="width: 100%; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 0.75rem; padding: 1rem; color: var(--gym-yellow); font-size: 1rem; cursor: not-allowed;" readonly>
                <p class="error-msg" id="error-member_code" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. 919876543210" required>
                <p class="error-msg" id="error-phone" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. john@example.com">
                <p class="error-msg" id="error-email" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Membership Plan</label>
                <select id="plan-selector" name="plan_id" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                    <option value="" disabled selected>Select a plan...</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" data-duration="{{ $plan->duration_days }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }} ({{ $plan->duration_days }} Days - ₹{{ number_format($plan->price, 2) }})
                        </option>
                    @endforeach
                </select>
                <p class="error-msg" id="error-plan_id" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Join Date</label>
                <input type="date" id="join-date" name="join_date" value="{{ old('join_date', date('Y-m-d')) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <p class="error-msg" id="error-join_date" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Expiry Date (Auto)</label>
                <input type="date" id="expiry-date" name="expiry_date" value="{{ old('expiry_date') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: var(--gym-yellow); font-size: 1rem;" required>
                <p class="error-msg" id="error-expiry_date" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                 <!-- Spacer -->
            </div>
        </div>

        <div style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.2); padding: 1rem; border-radius: 0.75rem; margin-bottom: 2rem;">
            <p style="color: var(--gym-yellow); font-size: 0.85rem; margin: 0; font-weight: 500;">
                <i class="fas fa-info-circle"></i> A one-time joining fee of <strong>₹200</strong> will be automatically added to the initial balance.
            </p>
        </div>

        <p style="opacity: 0.5; font-size: 0.8rem; margin-bottom: 1.5rem;">Note: Expiry date will be automatically calculated based on the selected plan duration.</p>

        <button type="submit" id="submit-btn" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            <span class="btn-text">REGISTER MEMBER</span>
            <i class="fas fa-circle-notch fa-spin btn-spinner" style="display:none;"></i>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    const form = document.getElementById('register-form');
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

    // Auto calculate expiry date
    const planSelector = document.getElementById('plan-selector');
    const joinDateInput = document.getElementById('join-date');
    const expiryDateInput = document.getElementById('expiry-date');

    function calculateExpiry() {
        if (!planSelector.value || !joinDateInput.value) return;

        const duration = parseInt(planSelector.options[planSelector.selectedIndex].dataset.duration);
        const joinDate = new Date(joinDateInput.value);
        
        if (isNaN(joinDate.getTime())) return;

        const expiryDate = new Date(joinDate);
        expiryDate.setDate(expiryDate.getDate() + duration);

        const yyyy = expiryDate.getFullYear();
        const mm = String(expiryDate.getMonth() + 1).padStart(2, '0');
        const dd = String(expiryDate.getDate()).padStart(2, '0');
        
        expiryDateInput.value = `${yyyy}-${mm}-${dd}`;
    }

    planSelector.addEventListener('change', calculateExpiry);
    joinDateInput.addEventListener('change', calculateExpiry);
    
    // Initial calculation if values exist
    calculateExpiry();
})();
</script>
@endpush
