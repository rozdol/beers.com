<?php
return [
    'Theme' => [
        'folder' => ROOT,
        'title' => 'AdminLTE',
        'logo' => [
            'mini' => getenv('PROJECT_NAME'),
            'large' => getenv('PROJECT_NAME'),
        ],
        'login' => [
            'show_remember' => true,
            'show_register' => false,
            'show_social' => false,
        ]
    ],
];
