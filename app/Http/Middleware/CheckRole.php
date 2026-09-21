<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Contoh pemakaian di route: ->middleware('role:admin')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $userRole = $user->role?->role_name;

        if ($userRole !== $role) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
