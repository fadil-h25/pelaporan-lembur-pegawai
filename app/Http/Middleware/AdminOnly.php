<?php
/**
 * DEPRECATED: Middleware ini tidak digunakan lagi.
 * System settings sekarang menggunakan config file, tidak ada lagi halaman admin
 * Middleware ini tetap ada untuk backward compatibility tapi tidak direkomendasikan untuk digunakan.
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;
        
        // Handle both enum and string comparison
        if ($userRole instanceof \App\UserRole) {
            $isAdmin = $userRole === \App\UserRole::ADMIN;
        } else {
            $isAdmin = $userRole === 'admin';
        }

        if (!$isAdmin) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
