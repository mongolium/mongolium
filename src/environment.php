<?php

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();
$dotenv->required([
    'DEVELOPMENT',
    'TOKEN_SECRET',
    'TOKEN_EXPIRY',
    'TOKEN_ISSUER',
    'MONGO_HOST',
    'MONGO_PORT',
    'MONGO_DATABASE',
    'MONGO_USERNAME',
    'MONGO_PASSWORD'
]);
