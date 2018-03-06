<?php

declare(strict_types = 1);

require '../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$app = require '../src/routes.php';

$app->run();
