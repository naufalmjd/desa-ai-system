<?php

declare(strict_types=1);

return [
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST')     ?: '127.0.0.1',
    'port'      => (int)(getenv('DB_PORT') ?: 3306),
    'dbname'    => getenv('DB_NAME')     ?: 'desa_ai_system',
    'username'  => getenv('DB_USER')     ?: 'root',
    'password'  => getenv('DB_PASS')     ?: '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options'   => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_STRINGIFY_FETCHES  => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone='+07:00'",
    ],
];
