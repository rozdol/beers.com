<?php

return [
    'Users.table' => 'Users',
    'Users.GoogleAuthenticator.login' => false,
    // disable remember-me functionality because currently it affects google authenticator functionality:
    // https://github.com/CakeDC/users/issues/488
    'Users.RememberMe.active' => false,
    // this is a workaround for disabling RememberMe functionality bug:
    // https://github.com/CakeDC/users/issues/490
    'Auth.authenticate' => [
        'all' => [
            'finder' => 'auth',
        ],
        'CakeDC/Users.ApiKey',
        // 'CakeDC/Users.RememberMe',
        'Form',
    ],
    'Users.avatar' => [
        'type' => 'App\Avatar\Type\ImageSource',
        'options' => [
            '{{size}}' => 160, // used in Gravatar to set the desired image size
            '{{default}}' => 'mm', // used in Gravatar to set the default/fallback themed image
            '{{rating}}' => 'g', // used in Gravatar to set the desired image appropriateness rating
            '{{src}}' => '/img/user-image-160x160.png' // used in ImageSource to set the default/fallback image
        ]
    ]
];
