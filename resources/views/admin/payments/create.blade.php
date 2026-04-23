@extends('layouts.admin')

@section('title', 'Record Payment')
@section('title_prefix', 'NEW')
@section('title_suffix', 'PAYMENT')

@section('header_actions')
<a href="{{ route('admin.payments.index') }}" class="btn btn-ghost">← Back</a>
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

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form id="payment-form" action="{{ route('admin.payments.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Select Plan</label>
            <select name="plan_id" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <option value="" disabled selected>Select a plan...</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ (old('plan_id') == $plan->id || (isset($selectedMember) && $selectedMember->plan_id == $plan->id)) ? 'selected' : '' }}>
                        {{ $plan->name }} (₹{{ number_format($plan->price, 2) }} - {{ $plan->duration_days }} Days)
                    </option>
                @endforeach
            </select>
            <p class="error-msg" id="error-plan_id" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Member</label>
            <select name="member_id" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <option value="" disabled selected>Select a member...</option>
                @foreach($members as $member)
                    @php
                        $balanceText = $member->balance < 0 ? 'Due: ₹' . number_format(abs($member->balance), 2) : ($member->balance > 0 ? 'Credit: ₹' . number_format($member->balance, 2) : 'Paid');
                    @endphp
                    <option value="{{ $member->id }}" {{ (isset($selectedMember) && $selectedMember->id == $member->id) ? 'selected' : '' }}>
                        {{ $member->name }} ({{ $member->member_code }} - {{ $balanceText }})
                    </option>
                @endforeach
            </select>
            <p class="error-msg" id="error-member_id" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Amount Received (₹)</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" placeholder="e.g. 1000" required>
            <p class="error-msg" id="error-amount" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
        </div>

        <div style="margin-bottom: 3rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Payment Date</label>
            <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
            <p class="error-msg" id="error-payment_date" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
        </div>

        <div class="card" style="background: rgba(255, 223, 0, 0.05); border-color: rgba(255, 223, 0, 0.1); padding: 1.5rem; margin-bottom: 2rem;">
            <p style="font-size: 0.875rem; line-height: 1.6; opacity: 0.8;">
                <strong>💡 Note:</strong> Recording this payment will automatically extend the member's expiry date based on their current plan duration and update their status.
            </p>
        </div>

        <button type="submit" id="submit-btn" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            <span class="btn-text">CONFIRM & RECORD PAYMENT</span>
            <i class="fas fa-circle-notch fa-spin btn-spinner" style="display:none;"></i>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    const form = document.getElementById('payment-form');
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
                    window.location.href = '{{ route("admin.payments.index") }}';
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
