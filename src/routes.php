<?php

use Helium\Middleware\Auth;

$app = new Slim\App($container);

$app->post('/token', 'TokenController:get');
$app->post('/token/update', 'TokenController:update')->add(Auth::class);
