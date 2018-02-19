<?php

$container['TokenController'] = function ($container) {
    return new Helium\Controllers\Token(
        new Helium\Services\Token(
            new ReallySimpleJWT\TokenBuilder,
            new ReallySimpleJWT\TokenValidator
        )
    );
};
