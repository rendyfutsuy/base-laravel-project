<?php

use Modules\Authentication\Http\Middleware\RefreshTokenMiddleware;
use Modules\Authentication\Http\Middleware\OTPValidationMiddleware;

return [
    'middleware' => [
        'otp-token' => OTPValidationMiddleware::class,
        'refresh-token' => RefreshTokenMiddleware::class,
    ],

    'paths' => [
        //  API
        [
            'path' => __DIR__.'/../routes/api.php',
            'middleware' => 'api',
            'prefix' => 'api/v1/authentication',
        ],
    ],
];
