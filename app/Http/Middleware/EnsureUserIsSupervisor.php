<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSupervisor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     // Pastikan user login
    //     if (!auth()->check()) {
    //         return redirect('/admin/login');
    //     }

    //     $user = auth()->user();

    //     // Cek apakah role adalah 'supervisor'
    //     if ($user->role !== 'supervisor') {
    //         abort(403, 'Unauthorized action.');
    //     }
    //     return $next($request);
    // }
}
