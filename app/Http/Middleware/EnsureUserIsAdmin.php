<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     * Ensure only USER / ADMIN role can perform modification actions.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || $user->isVisitor()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: Akun VISITOR hanya memiliki hak akses melihat data (Read-Only). Fitur Tambah, Edit, Hapus, dan Import hanya dapat dilakukan oleh akun USER (ADMIN).'
                ], 403);
            }

            return redirect()->back()->with('error', 'Akses Ditolak: Akun VISITOR hanya memiliki hak akses melihat data (Read-Only).');
        }

        return $next($request);
    }
}
