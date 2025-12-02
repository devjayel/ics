<?php

namespace App\Http\Middleware;

use App\Models\Personnel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PersonnelMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->extractToken($request);
        if (!$token) {
            return response()->json(['message' => 'Unauthorized: token missing'], 401);
        }

        $exists = Personnel::where('token', $token)->exists();
        if (!$exists) {
            return response()->json(['message' => 'Unauthorized: token invalid'], 401);
        }

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization');
        if ($header && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return $request->header('X-Token') ?? $request->query('token');
    }
}
