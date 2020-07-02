<?php

declare(strict_types=1);

return [
    'cache' => [
        'api' => [
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'namespace' => '',
                    'cache_dir' => 'data/cache/api',
                    'dir_level' => 1,
                    'dir_permission' => 0777,
                    'file_permission' => 0666,
                    'ttl' => 300,
                ],
            ],
            'plugins' => [
                'serializer',
                'exception_handler' => ['throw_exceptions' => false],
            ],
        ]
    ],
];
