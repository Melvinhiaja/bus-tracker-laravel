<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
    
        if (!$user) {
            return redirect()->route('login');
        }
    
        $userRole = $user->role;
    
        // Admin boleh akses semua
        if ($userRole === 'admin') {
            return $next($request);
        }
    
        // ✅ Langsung cek apakah role user masuk dalam daftar yang diizinkan
        if (in_array($userRole, $roles)) {
            return $next($request);
        }
    
        return redirect()->route('unauthorized')->with('error', 'Akses ditolak.');
    }
    
}
