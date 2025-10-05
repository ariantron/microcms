<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for JWT authentication.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | JWT Secret Key
    |--------------------------------------------------------------------------
    |
    | The secret key used to sign JWT tokens. This should be a secure random
    | string. You can generate one using: php artisan key:generate
    |
    */
    'secret' => env('JWT_SECRET', env('APP_KEY')),

    /*
    |--------------------------------------------------------------------------
    | JWT Algorithm
    |--------------------------------------------------------------------------
    |
    | The algorithm used to sign JWT tokens. Supported algorithms:
    | HS256, HS384, HS512, RS256, RS384, RS512
    |
    */
    'algorithm' => env('JWT_ALGORITHM', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | JWT Token Expiration
    |--------------------------------------------------------------------------
    |
    | The time in seconds after which the JWT token will expire.
    | Default is 24 hours (86400 seconds).
    |
    */
    'expiration' => env('JWT_EXPIRATION', 86400),

    /*
    |--------------------------------------------------------------------------
    | JWT Refresh Token Expiration
    |--------------------------------------------------------------------------
    |
    | The time in seconds after which the refresh token will expire.
    | Default is 7 days (604800 seconds).
    |
    */
    'refresh_expiration' => env('JWT_REFRESH_EXPIRATION', 604800),

    /*
    |--------------------------------------------------------------------------
    | JWT Issuer
    |--------------------------------------------------------------------------
    |
    | The issuer of the JWT token. Usually your application URL.
    |
    */
    'issuer' => env('JWT_ISSUER', env('APP_URL')),

    /*
    |--------------------------------------------------------------------------
    | JWT Audience
    |--------------------------------------------------------------------------
    |
    | The audience of the JWT token. Usually your application URL.
    |
    */
    'audience' => env('JWT_AUDIENCE', env('APP_URL')),

    /*
    |--------------------------------------------------------------------------
    | JWT Leeway
    |--------------------------------------------------------------------------
    |
    | The leeway in seconds to account for clock skew between servers.
    | Default is 60 seconds.
    |
    */
    'leeway' => env('JWT_LEEWAY', 60),
];
