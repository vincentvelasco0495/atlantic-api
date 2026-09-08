<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandlePreflightRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getMethod() !== 'OPTIONS') {
            return $next($request);
        }

        $origin = $request->headers->get('Origin', '');
        $allowedOrigin = $this->resolveAllowedOrigin($origin);

        return response('', 204)
            ->header('Access-Control-Allow-Origin', $allowedOrigin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, X-XSRF-TOKEN')
            ->header('Access-Control-Max-Age', '86400');
    }

    private function resolveAllowedOrigin(string $origin): string
    {
        $allowedOrigins = config('cors.allowed_origins', ['*']);

        if (in_array('*', $allowedOrigins, true)) {
            return $origin !== '' ? $origin : '*';
        }

        if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
            return $origin;
        }

        return $allowedOrigins[0] ?? '*';
    }
}
