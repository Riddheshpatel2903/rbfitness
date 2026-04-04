<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('rbadmin/login') || $request->is('rbadmin/otp')) {
            return $next($request);
        }

        if (!auth()->check() || (!$request->session()->get('admin_otp_verified') && config('app.env') !== 'local')) {
            return redirect()->route('admin.otp.show');
        }

        return $next($request);
    }
}
