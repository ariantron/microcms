<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Responses\ServiceResponse;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * JWT secret key from config.
     */
    protected string $secret;

    /**
     * JWT algorithm.
     */
    protected string $algorithm = 'HS256';

    /**
     * Token expiration time in seconds (default: 24 hours).
     */
    protected int $expiration = 86400;

    public function __construct()
    {
        $this->secret = config('jwt.secret', config('app.key'));
        $this->algorithm = config('jwt.algorithm', 'HS256');
        $this->expiration = config('jwt.expiration', 86400);
    }

    /**
     * Generate JWT token for user.
     */
    public function generateToken(User $user): string
    {
        $payload = [
            'iss' => config('app.url'), // Issuer
            'aud' => config('app.url'), // Audience
            'iat' => time(), // Issued at
            'exp' => time() + $this->expiration, // Expiration
            'sub' => $user->id, // Subject (user ID)
            'user' => $user->payload(),
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Validate JWT token and return user.
     */
    public function validateToken(string $token): ?User
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));

            // Check if token is expired
            if (isset($decoded->exp) && $decoded->exp < time()) {
                return null;
            }

            // Get user from token subject
            if (isset($decoded->sub)) {
                return User::find($decoded->sub);
            }

            return null;
        } catch (Exception $e) {
            Log::error('JWT validation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Authenticate user with mobile and password.
     */
    public function authenticate(string $mobile, string $password): ?User
    {
        $user = User::where('mobile', $mobile)->first();

        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }

        return null;
    }

    /**
     * Login user and return token.
     */
    public function login(string $mobile, string $password): ServiceResponse
    {
        $user = $this->authenticate($mobile, $password);

        if (!$user) {
            return ServiceResponse::failed(['Invalid credentials']);
        }

        $token = $this->generateToken($user);

        return ServiceResponse::success([
            'message' => 'Login successful',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Get user from token.
     */
    public function getUserFromToken(string $token): ?User
    {
        return $this->validateToken($token);
    }
}
