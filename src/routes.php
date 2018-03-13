<?php

use Mongolium\Middleware\Auth;
use Mongolium\Middleware\Password;
use Mongolium\Middleware\Basic;

$container  = require __DIR__ . '/../src/container.php';

$app = new Slim\App($container);

$app->group('', function () use ($app) {
    $app->post('/token', 'TokenController:create')->add(Password::class);
    $app->patch('/token', 'TokenController:update')->add(Auth::class);

    $app->get('/user', 'UserController:read')->add(Auth::class);
    $app->get('/user/{id}', 'UserController:read')->add(Auth::class);
    $app->post('/user', 'UserController:create')->add(Auth::class);
})->add(Basic::class);

return $app;
