<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Mail\OtpMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function showOtpForm()
    {
        return view('admin.auth.otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('admin.login.show');
        }

        $otpRecord = Otp::where('user_id', $user->id)->first();

        if (!$otpRecord || !Hash::check($request->otp, $otpRecord->otp)) {
            return back()->withErrors(['otp' => 'The provided OTP is incorrect.']);
        }

        if (Carbon::now()->gt($otpRecord->expires_at)) {
            return back()->withErrors(['otp' => 'The OTP has expired. Please request a new one.']);
        }

        // OTP Valid - mark as verified
        $request->session()->put('admin_otp_verified', true);
        
        // Single use - delete after success
        $otpRecord->delete();

        return redirect()->route('admin.dashboard');
    }

    public function resend(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('admin.login.show');
        }

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Otp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(5),
            ]
        );

        Mail::to($user->email)->send(new OtpMail($otp));

        return back()->with('status', 'A new OTP has been sent to your email.');
    }
}
