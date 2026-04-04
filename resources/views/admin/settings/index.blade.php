@extends('layouts.admin')

@section('title', 'Site Settings')
@section('title_prefix', 'GYM')
@section('title_suffix', 'SETTINGS')

@section('content')
<div class="card" style="margin: 0 auto;">
    <form action="{{ route('admin.settings.store') }}" method="POST">
        @csrf

        <h3 style="font-family: 'Oswald', sans-serif; text-transform: uppercase; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; color: var(--gym-yellow);">Contact Information</h3>
        
        <div class="responsive-grid" style="margin-bottom: 2rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Phone Number</label>
                <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '+91 91730 82488' }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;">
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">WhatsApp Number</label>
                <input type="text" name="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '919173082488' }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;">
            </div>
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Address</label>
            <input type="text" name="contact_address" value="{{ $settings['contact_address'] ?? 'Atmiya Complex, Gandevi, Navsari, Gujarat - 396360' }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;">
        </div>

        <h3 style="font-family: 'Oswald', sans-serif; text-transform: uppercase; margin-top: 3rem; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; color: var(--gym-yellow);">Operating Hours</h3>

        <div class="responsive-grid" style="margin-bottom: 3rem;">
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Mon–Sat</label>
                <input type="text" name="hours_mon_sat" value="{{ $settings['hours_mon_sat'] ?? '5:00 AM – 10:00 PM' }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;">
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; text-transform: uppercase; opacity: 0.6; margin-bottom: 0.75rem;">Sunday</label>
                <input type="text" name="hours_sun" value="{{ $settings['hours_sun'] ?? '7:00 AM – 6:00 PM' }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 1rem; color: #fff; font-size: 1rem;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.25rem;">
            SAVE ALL SETTINGS
        </button>
    </form>
</div>
@endsection
