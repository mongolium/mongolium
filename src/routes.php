<?php

use Mongolium\Middleware\Auth;
use Mongolium\Middleware\Password;
use Mongolium\Middleware\Basic;

$container  = require __DIR__ . '/../src/container.php';

$app = new Slim\App($container);

$app->group('', function () use ($app) {
    $app->post('/token', 'TokenController:create')->add(Password::class);
    $app->patch('/token', 'TokenController:update')->add(Auth::class);

    $app->get('/admins', 'AdminController:read')->add(Auth::class);
    $app->get('/admins/{id}', 'AdminController:read')->add(Auth::class);
    $app->post('/admins', 'AdminController:create')->add(Auth::class);

    $app->get('/posts', 'PostController:read');
    $app->get('/posts/{id}', 'PostController:readOne');
    $app->post('/posts', 'PostController:create')->add(Auth::class);

    $app->get('/pages', 'PageController:read');
    $app->get('/pages/{id}', 'PageController:read');
    $app->post('/pages', 'PageController:create')->add(Auth::class);
})->add(Basic::class);

return $app;
