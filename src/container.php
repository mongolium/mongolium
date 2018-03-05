<?php

$container['Orm'] = function ($container) {
    return new Helium\Services\Db\Orm(
        Helium\Services\Db\Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'))
    );
};

$container['TokenController'] = function ($container) {
    return new Helium\Controllers\Token(
        new Helium\Services\Token(
            new ReallySimpleJWT\TokenBuilder,
            new ReallySimpleJWT\TokenValidator,
            $container['Orm']
        )
    );
};

$container['UserController'] = function ($container) {
    return new Helium\Controllers\User(
        new Helium\Services\User(
            $container['Orm']
        )
    );
};
