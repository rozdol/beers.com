<?php

return [
    'Avatar' => [
        'default' => 'App\Avatar\Type\ImageSource',
        'options' => [
            'App\Avatar\Type\ImageSource' => [
                '{{src}}' => '/img/user-image-160x160.png' // sets the default/fallback image
            ],
            'App\Avatar\Type\Gravatar' => [
                '{{size}}' => 160, // sets the desired image size
                '{{default}}' => 'mm', // sets the default/fallback themed image
                '{{rating}}' => 'g', // sets the desired image appropriateness rating
            ]
        ]
    ]
];
