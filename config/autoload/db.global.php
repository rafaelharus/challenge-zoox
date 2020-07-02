<?php

declare(strict_types=1);

return [
    'db-mongo' => [
        'uri' => getenv('MONGOURI'),
        'dbname' => getenv('DBNAME'),
    ],
];
