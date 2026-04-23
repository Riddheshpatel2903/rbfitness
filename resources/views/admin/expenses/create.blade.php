@extends('layouts.admin')

@section('title', 'Add Expense')
@section('title_prefix', 'NEW')
@section('title_suffix', 'EXPENSE')

@section('header_actions')
<a href="{{ route('admin.expenses.index') }}" class="btn btn-ghost">← Back</a>
@endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form id="expense-form" action="{{ route('admin.expenses.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Expense Title</label>
            <input type="text" name="title" placeholder="e.g. Electricity Bill, Rent, Staff Salary" 
                   style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
            <p class="error-msg" id="error-title" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Amount (₹)</label>
                <input type="number" step="0.01" name="amount" placeholder="0.00" 
                       style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <p class="error-msg" id="error-amount" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Category</label>
                <select name="category" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;">
                    <option value="Rent">Rent</option>
                    <option value="Electricity">Electricity</option>
                    <option value="Salary">Salary</option>
                    <option value="Equipment">Equipment</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Others">Others</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Transaction Date</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" 
                       style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <p class="error-msg" id="error-transaction_date" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Payment Method</label>
                <select name="payment_method" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;">
                    <option value="Cash">Cash</option>
                    <option value="UPI / GPay / PhonePe">UPI / GPay / PhonePe</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Card">Card</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 3rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Description / Details</label>
            <textarea name="description" rows="4" placeholder="Where was it spent? Any other details..." 
                      style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem; resize: none;"></textarea>
            <p class="error-msg" id="error-description" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
        </div>

        <button type="submit" id="submit-btn" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            <span class="btn-text">SAVE RECORD</span>
            <i class="fas fa-circle-notch fa-spin btn-spinner" style="display:none;"></i>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    const form = document.getElementById('expense-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnSpinner = submitBtn.querySelector('.btn-spinner');

    form.onsubmit = async (e) => {
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

            if (response.ok) {
                window.location.href = '{{ route("admin.expenses.index") }}';
            } else if (data.errors) {
                Object.entries(data.errors).forEach(([key, msgs]) => {
                    const errEl = document.getElementById(`error-${key}`);
                    if (errEl) {
                        errEl.textContent = msgs[0];
                        errEl.style.display = 'block';
                    }
                });
            }
        } catch (err) {
            console.error(err);
        } finally {
            submitBtn.disabled = false;
            btnText.style.opacity = '1';
            btnSpinner.style.display = 'none';
        }
    };
})();
</script>
@endpush
