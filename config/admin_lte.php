<?php
/**
 * AdminLTE plugin configuration
 */

// get logo path
$path = WWW_ROOT . 'img' . DS . 'logo.png';

// convert to base64 image
$data = file_get_contents($path);
$base64 = 'data:image/png;base64,' . base64_encode($data);

// create logo html img
$logo = '<img src="' . $base64 . '" alt="Site Logo" />';

return [
    'Theme' => [
        'folder' => ROOT,
        'title' => getenv('PROJECT_NAME'),
        'logo' => [
            'mini' => $logo,
            'large' => $logo,
        ],
        'login' => [
            'show_remember' => true,
            'show_register' => false,
            'show_social' => false,
        ]
    ],
];
