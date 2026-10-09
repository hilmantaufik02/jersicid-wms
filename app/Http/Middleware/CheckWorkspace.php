<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWorkspace
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$workspaces): Response
    {
        // Cek apakah user sudah login
        if (!$request->user()) {
            abort(403, 'Unauthorized');
        }

        // ✅ PERBAIKAN: OWNER memiliki full access ke semua modul
        // Sesuai PRD v3.0: "OWNER/SYSTEM MANAGER - Full access seluruh modul"
        if ($request->user()->role === 'OWNER') {
            return $next($request);
        }

        // Cek workspace user untuk role selain OWNER
        $userWorkspace = $request->user()->workspace_default;

        if (!in_array($userWorkspace, $workspaces)) {
            abort(403, 'WORKSPACE ANDA TIDAK MEMILIKI AKSES KE MODUL INI.');
        }

        return $next($request);
    }
}
