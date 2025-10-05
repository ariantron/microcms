<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTimeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $response = $next($request);

        // Only add timing information to JSON responses
        if ($response->headers->get('Content-Type') === 'application/json' ||
            str_contains($response->headers->get('Content-Type', ''), 'application/json')) {

            $endTime = microtime(true);
            $responseTime = round($endTime - $startTime, 6);

            $response->headers->set('X-Response-Time', $responseTime);
            $response->headers->set('X-Response-Timestamp', now()->toISOString());

            $content = $response->getContent();
            if ($content && is_string($content)) {
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $decoded['timestamp'] = now()->toISOString();
                    $decoded['response_time'] = $responseTime; // float in seconds
                    $response->setContent(json_encode($decoded));
                }
            }
        }

        return $response;
    }
}
