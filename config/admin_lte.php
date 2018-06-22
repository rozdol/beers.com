<?php
/**
 * AdminLTE plugin configuration
 */

// create logo HTML img tags
$logo = '<img src="/img/logo.png" alt="Site Logo" />';
$logoMini = '<img src="/img/logo-mini.png" alt="Site Logo" />';

return [
    'Theme' => [
        'folder' => ROOT,
        'title' => getenv('PROJECT_NAME'),
        'logo' => [
            'mini' => $logoMini,
            'large' => $logo,
        ],
        'login' => [
            'show_remember' => true,
            'show_register' => false,
            'show_social' => false,
        ],
        'version' => 'dark'
    ],
];
