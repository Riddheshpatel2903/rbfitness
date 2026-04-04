<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Mail\OtpMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['email' => "Too many login attempts. Please try again in $seconds seconds."]);
        }

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            $user = Auth::user();

            // BYPASS OTP FOR LOCAL DEVELOPMENT
            if (config('app.env') === 'local') {
                $request->session()->put('admin_otp_verified', true);
                return redirect()->route('admin.dashboard');
            }
            
            // Generate OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Save OTP with 5-minute expiry
            Otp::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'otp' => Hash::make($otp),
                    'expires_at' => Carbon::now()->addMinutes(5),
                ]
            );

            // Send Email
            Mail::to($user->email)->send(new OtpMail($otp));

            $request->session()->put('admin_auth_user_id', $user->id);
            $request->session()->put('admin_otp_verified', false);

            return redirect()->route('admin.otp.show');
        }

        RateLimiter::hit($throttleKey);
        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login.show');
    }
}
