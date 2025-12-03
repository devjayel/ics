<?php

namespace App\Http\Middleware;

use App\Models\Rul;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RulMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->extractToken($request);
        if (!$token) {
            return response()->json(['message' => 'Unauthorized: token missing'], 401);
        }

        $exists = Rul::where('token', $token)->exists();
        if (!$exists) {
            return response()->json(['message' => 'Unauthorized: token invalid'], 401);
        }

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        // Use Laravel helper for bearer token, fall back to header X-Token or query token
        return $request->bearerToken() ?? $request->header('X-Token') ?? $request->query('token');
    }
}
