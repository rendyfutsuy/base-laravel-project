<?php

return [
    'paths' => [
        //  API
        [
            'path' => base_path('routes/api/unvalidated.php'),
            'middleware' => 'api',
            'prefix' => 'api',
        ],
    
        [
            'path' => base_path('routes/api.php'),
            'middleware' => 'api',
            'prefix' => 'api',
        ],

        // WEB
        [
            'path' => base_path('routes/web.php'),
            'middleware' => 'web',
            'prefix' => null,
        ],
    ]
];