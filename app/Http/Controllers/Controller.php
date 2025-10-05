<?php

namespace App\Http\Controllers;

use App\Responses\ServiceResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    public function respond(
        ServiceResponse $serviceResponse,
        int $status = Response::HTTP_OK
    ): JsonResponse
    {
        return response()->json($serviceResponse->toArray(), $status);
    }
}
