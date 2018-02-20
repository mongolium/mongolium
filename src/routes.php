<?php

use Helium\Middleware\Auth;

$app = new Slim\App($container);

$app->post('/token', 'TokenController:create');
$app->patch('/token', 'TokenController:update')->add(Auth::class);
