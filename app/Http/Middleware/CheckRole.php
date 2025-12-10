<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
{
    // 1. Cek apakah user sudah login?
    if (! $request->user()) {
        return redirect('/login');
    }

    // --- LOGIKA BARU (BYPASS ADMIN) ---
    // Jika user adalah 'admin', izinkan lewat kemana saja (God Mode)
    if ($request->user()->role === 'admin') {
        return $next($request);
    }
    // ----------------------------------

    // 2. Jika bukan admin, baru kita cek apakah role-nya sesuai
    if ($request->user()->role !== $role) {
        abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
    }

    return $next($request);
}
}