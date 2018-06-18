<?php
// Roles and Capabilities plugin configuration
return [
    'RolesCapabilities' => [
        'ownerCheck' => [
            // List of tables that should be skipped during record access check.
            'skipTables' => [
                'byInstance' => [
                    Menu\Model\Table\MenuItemsTable::class,
                    Menu\Model\Table\MenusTable::class
                ]
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
