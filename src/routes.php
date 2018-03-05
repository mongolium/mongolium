<?php

use Helium\Middleware\Auth;
use Helium\Middleware\Password;
use Helium\Middleware\Basic;

$app = new Slim\App($container);

$app->group('', function () use ($app) {

    $app->post('/token', 'TokenController:create')->add(Password::class);
    $app->patch('/token', 'TokenController:update')->add(Auth::class);

    $app->post('/user', 'UserController:create')->add(Auth::class);

})->add(Basic::class);
