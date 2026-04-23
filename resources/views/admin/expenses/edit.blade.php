@extends('layouts.admin')

@section('title', 'Edit Expense')
@section('title_prefix', 'MODIFY')
@section('title_suffix', 'RECORD')

@section('header_actions')
<a href="{{ route('admin.expenses.index') }}" class="btn btn-ghost">← Back</a>
@endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form id="expense-form" action="{{ route('admin.expenses.update', $expense->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Expense Title</label>
            <input type="text" name="title" value="{{ $expense->title }}"
                   style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
            <p class="error-msg" id="error-title" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Amount (₹)</label>
                <input type="number" step="0.01" name="amount" value="{{ $expense->amount }}"
                       style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <p class="error-msg" id="error-amount" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Category</label>
                <select name="category" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;">
                    @foreach(['Rent', 'Electricity', 'Salary', 'Equipment', 'Maintenance', 'Marketing', 'Others'] as $cat)
                        <option value="{{ $cat }}" {{ $expense->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Transaction Date</label>
                <input type="date" name="transaction_date" value="{{ $expense->transaction_date->format('Y-m-d') }}" 
                       style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;" required>
                <p class="error-msg" id="error-transaction_date" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Payment Method</label>
                <select name="payment_method" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;">
                    @foreach(['Cash', 'UPI / GPay / PhonePe', 'Bank Transfer', 'Cheque', 'Card'] as $method)
                        <option value="{{ $method }}" {{ $expense->payment_method == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom: 3rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Description / Details</label>
            <textarea name="description" rows="4" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem; resize: none;">{{ $expense->description }}</textarea>
            <p class="error-msg" id="error-description" style="color: #ff4d4d; font-size: 0.8rem; margin-top: 0.5rem; display:none;"></p>
        </div>

        <button type="submit" id="submit-btn" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            <span class="btn-text">UPDATE RECORD</span>
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
