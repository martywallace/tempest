<?php

/**
 * Your application configuration, where data like API keys, database connection details and environment settings can be
 * defined. Configuration cascades based on the environment your application is running in.
 */

return [
    '*' => [
        'templates' => [
            'app/templates'
        ]
    ],

    'localhost' => [
        'dev' => true,
        'url' => 'http://localhost:3000'
    ]
];