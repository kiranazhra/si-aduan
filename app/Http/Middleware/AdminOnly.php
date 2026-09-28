<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Halaman /admin hanya untuk Super Admin, Operator, dan Viewer.
 * Petugas unit diarahkan ke portal unit; akun nonaktif dikeluarkan.
 */
class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $pengguna = $request->user();

        if (! $pengguna || ! $pengguna->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi administrator.']);
        }

        if ($pengguna->bolehMenanggapi()) {
            return redirect()->route('unit.dashboard');
        }

        return $next($request);
    }
}
