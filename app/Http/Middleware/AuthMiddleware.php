<?php

namespace App\Http\Middleware;

use App\Responses\ServiceResponse;
use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            $response = ServiceResponse::failed([
                'Token not provided'
            ]);
            return response()->json($response->toArray(), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->authService->getUserFromToken($token);

        if (!$user) {
            $response = ServiceResponse::failed([
                'Invalid or expired token'
            ]);
            return response()->json($response->toArray(), Response::HTTP_UNAUTHORIZED);
        }

        // Set the authenticated user
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
