<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSecurityHeaders
{
    /**
     * Handle an incoming request and apply production-grade security headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME-sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Clickjacking protection (allow same origin framing if needed)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // XSS protection for older legacy browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Permissions policy
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), payment=()');

        return $response;
    }
}
