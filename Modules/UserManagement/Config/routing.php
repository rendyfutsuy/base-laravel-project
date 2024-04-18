<?php

return [
    'middleware' => [
        //
    ],

    'paths' => [
        [
            'path' => __DIR__.'/../routes/user.php',
            'middleware' => 'api',
            'prefix' => 'api/v1/user-management',
        ],

        [
            'path' => __DIR__.'/../routes/staff.php',
            'middleware' => 'api',
            'prefix' => 'api/v1/user-management',
        ],

        [
            'path' => __DIR__.'/../routes/admin.php',
            'middleware' => 'api',
            'prefix' => 'api/v1/user-management',
        ],
    ],
];
