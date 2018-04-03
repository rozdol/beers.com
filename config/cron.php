<?php

return [
    'Cron' => [
        'CakeShell' => [
            'skipFiles' => [
                'ConsoleShell',
                'FakerShell',
                'PluginShell',
                'CronShell',
            ],
            'skipPlugins' => [
                'Bake',
            ],
        ],
    ]
];
