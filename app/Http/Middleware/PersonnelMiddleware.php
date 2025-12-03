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
            return response()->json([
                'success' => false,
                'message' => 'You must login first as Personnel.'
            ], 401);
        }

        $personnel = Personnel::where('token', $token)->first();
        if (!$personnel) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to access this resource.'
            ], 401);
        }

        $request->setUserResolver(function () use ($personnel) {
            return $personnel;
        });

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
