<?php

namespace App\Http\Middleware;

use App\Support\WorkspaceSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsolateAdminWorkspace
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        WorkspaceSession::enterAdmin($request);

        return $next($request);
    }
}
