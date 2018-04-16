<?php

$config = require __DIR__ . '/../src/config.php';

$container = new \Slim\Container($config);

$container['Orm'] = function ($container) {
    return new Mongolium\Core\Services\Db\Orm(
        Mongolium\Core\Services\Db\Client::getInstance(
            getenv('MONGO_HOST'),
            getenv('MONGO_PORT'),
            getenv('MONGO_DATABASE'),
            getenv('MONGO_USERNAME'),
            getenv('MONGO_PASSWORD')
        )
    );
};

$container['TokenController'] = function ($container) {
    return new Mongolium\Core\Controllers\Token(
        new Mongolium\Core\Services\Token(
            new ReallySimpleJWT\TokenBuilder,
            new ReallySimpleJWT\TokenValidator,
            $container['Orm']
        )
    );
};

$container['AdminController'] = function ($container) {
    return new Mongolium\Core\Controllers\Admin(
        new Mongolium\Core\Services\Admin(
            $container['Orm']
        )
    );
};

$container['PostController'] = function ($container) {
    return new Mongolium\Core\Controllers\Post(
        new Mongolium\Core\Services\Post(
            $container['Orm']
        )
    );
};

$container['PageController'] = function ($container) {
    return new Mongolium\Core\Controllers\Page(
        new Mongolium\Core\Services\Page(
            $container['Orm']
        )
    );
};

return $container;
