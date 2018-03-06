<?php

declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$app = require __DIR__ . '/../src/routes.php';

$app->run();
