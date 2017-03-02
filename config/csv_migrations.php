<?php
use Cake\Core\Configure;

// CsvMigrations plugin configuration
return [
    'CsvMigrations' => [
        'api' => [
            'auth' => Configure::read('API.auth')
        ],
        'acl' => [
            'class' => 'RolesCapabilities.Capabilities',
            'method' => 'checkAccess',
            'component' => 'RolesCapabilities.Capability'
        ]
    ]
];
