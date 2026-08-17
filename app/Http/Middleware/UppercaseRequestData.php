<?php

namespace App\Http\Middleware;

use App\Support\UppercaseText;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UppercaseRequestData
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('POST') && ! $request->isMethod('PUT') && ! $request->isMethod('PATCH')) {
            return $next($request);
        }

        $request->merge(UppercaseText::convertPayload($request->except(['_token'])));

        return $next($request);
    }
}
