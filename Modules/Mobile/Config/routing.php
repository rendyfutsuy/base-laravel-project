<?php

return [
    'middleware' => [
        //
    ],

    'paths' => [
        [
            'path' => __DIR__.'/../routes/authentication/auth.php',
            'middleware' => 'api',
            'prefix' => 'api/v1/mobile/mobile/authentication',
        ],

        [
            'path' => __DIR__.'/../routes/user-management/user.php',
            'middleware' => 'api',
            'prefix' => 'api/v1/mobile/user-management',
        ],

        [
            'path' => __DIR__.'/../routes/user-management/staff.php',
            'middleware' => 'api',
            'prefix' => 'api/v1/mobile/user-management',
        ],

        [
            'path' => __DIR__.'/../routes/user-management/admin.php',
            'middleware' => 'api',
            'prefix' => 'api/v1/mobile/user-management',
        ],

        [
            'path' => __DIR__.'/../routes/notification/notification.php',
            'middleware' => 'api',
            'prefix' => 'api/v1/mobile/notification',
        ],
    ],
];
