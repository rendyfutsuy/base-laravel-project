<?php

return [
    'paths' => [
        //  API
        [
            'path' => base_path('routes/api/auth.php'),
            'middleware' => 'api',
            'prefix' => 'api',
        ],

        [
            'path' => base_path('routes/api/profile.php'),
            'middleware' => 'api',
            'prefix' => 'api',
        ],
    
        [
            'path' => base_path('routes/api/user.php'),
            'middleware' => 'api',
            'prefix' => 'api',
        ],
    
        [
            'path' => base_path('routes/api/staff.php'),
            'middleware' => 'api',
            'prefix' => 'api',
        ],
    
        [
            'path' => base_path('routes/api/admin.php'),
            'middleware' => 'api',
            'prefix' => 'api',
        ],
    
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

        [
            'path' => base_path('routes/api/role_permission.php'),
            'middleware' => 'api',
            'prefix' => 'api',
        ],
    
        [
            'path' => base_path('routes/example.php'),
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