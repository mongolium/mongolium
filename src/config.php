<?php

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$config = [
    'settings' => [
        'displayErrorDetails' => getenv('DEVELOPMENT'),
    ]
];

$container = new \Slim\Container($config);
