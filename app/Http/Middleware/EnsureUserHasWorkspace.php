<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasWorkspace
{
    public function handle(Request $request, Closure $next, string ...$workspaces): Response
    {
        if (! $request->user() || ! in_array($request->user()->workspace_default->value, $workspaces)) {
            abort(403, 'Akses ditolak. Workspace ini tidak sesuai dengan hak akses Anda.');
        }

        return $next($request);
    }
}
