<?php

return [
    'default' => 'file',
    
    'channels' => [
        'file' => [
            'driver' => 'single',
            'path' => 'logs/mediabox.log',
            'level' => 'debug',
        ],
        
        'error' => [
            'driver' => 'single',
            'path' => 'logs/error.log',
            'level' => 'error',
        ],
        
        'security' => [
            'driver' => 'single',
            'path' => 'logs/security.log',
            'level' => 'warning',
        ]
    ],
    
    'retention' => [
        'days' => 30,
        'size' => '100MB'
    ]
];
