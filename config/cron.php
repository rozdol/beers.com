<?php

return [
    'Cron' => [
        'CakeShell' => [
            'skipFiles' => [
                'ConsoleShell',
                'FakerShell',
                'PluginShell',
            ],
            'skipPlugins' => [
                'Bake',
            ],
        ],
    ]
];
