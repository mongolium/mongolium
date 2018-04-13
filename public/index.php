<?php

declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../src/environment.php';

$app = require __DIR__ . '/../src/routes.php';

$app->run();
