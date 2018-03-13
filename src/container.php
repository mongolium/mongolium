<?php

$config = require __DIR__ . '/../src/config.php';

$container = new \Slim\Container($config);

$container['Orm'] = function ($container) {
    return new Mongolium\Services\Db\Orm(
        Mongolium\Services\Db\Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'))
    );
};

$container['TokenController'] = function ($container) {
    return new Mongolium\Controllers\Token(
        new Mongolium\Services\Token(
            new ReallySimpleJWT\TokenBuilder,
            new ReallySimpleJWT\TokenValidator,
            $container['Orm']
        )
    );
};

$container['AdminController'] = function ($container) {
    return new Mongolium\Controllers\Admin(
        new Mongolium\Services\Admin(
            $container['Orm']
        )
    );
};

return $container;
