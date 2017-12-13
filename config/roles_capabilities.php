<?php
// Roles and Capabilities plugin configuration
return [
    'RolesCapabilities' => [
        'ownerCheck' => [
            // List of tables that should be skipped during record access check.
            'skipTables' => [
                'menus',
                'menu_items'
            ],
        ],
        'accessCheck' => [
            'skipActions' => [
                'App\Controller\SystemController' => [
                    'error',
                ]
            ],
        ],
    ]
];
