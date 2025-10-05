<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Responses\ServiceResponse;
use App\Services\AuthService;
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login user with mobile and password.
     */
    public function login(LoginRequest $request)
    {
        try {
            $response = $this->authService->login(
                $request->input('mobile'),
                $request->input('password')
            );
            return $this->respond(
                $response,
                $response->success ? Response::HTTP_OK : Response::HTTP_UNAUTHORIZED,
            );
        } catch (Exception $e) {
            Log::error('Error occurred during login', [
                'mobile' => $request->input('mobile'),
                'exception' => $e->getMessage(),
            ]);
            return $this->respond(
                ServiceResponse::failed([
                    'An error occurred during login. Please try again later.'
                ]),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
