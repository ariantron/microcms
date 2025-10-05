<?php

namespace App\Exceptions;

use App\Responses\ServiceResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiException
{
    /**
     * Handle 404 Not Found exceptions
     */
    public static function handleNotFound(NotFoundHttpException $e, Request $request): ?JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $response = ServiceResponse::failed(['Resource not found']);
            return response()->json($response->toArray(), Response::HTTP_NOT_FOUND);
        }

        return null;
    }

    /**
     * Handle 405 Method Not Allowed exceptions
     */
    public static function handleMethodNotAllowed(MethodNotAllowedHttpException $e, Request $request): ?JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $response = ServiceResponse::failed(['Method not allowed']);
            return response()->json($response->toArray(), Response::HTTP_METHOD_NOT_ALLOWED);
        }

        return null;
    }
}
