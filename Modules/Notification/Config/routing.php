<?php

return [
    'middleware' => [
        //
    ],

    'paths' => [
        //  API
        [
            'path' => __DIR__.'/../routes/api.php',
            'middleware' => 'api',
            'prefix' => 'api/v1/notification',
        ],
    ],
];
