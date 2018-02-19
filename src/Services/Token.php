<?php

namespace Helium\Services;

use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use Carbon\Carbon;
use stdClass;

class Token
{
    private $tokenBuilder;

    private $tokenValidator;

    public function __construct(TokenBuilder $tokenBuilder, TokenValidator $tokenValidator)
    {
        $this->tokenBuilder = $tokenBuilder;

        $this->tokenValidator = $tokenValidator;
    }

    public function getToken(int $userId, string $userType, string $secret, int $minutes, string $issuer): string
    {
        return $this->tokenBuilder->addPayload(['key' => 'user_id', 'value' => $userId])
            ->addPayload(['key' => 'user_type', 'value' => $userType])
            ->setSecret($secret)
            ->setExpiration(Carbon::now()->addMinutes($minutes)->toDateTimeString())
            ->setIssuer($issuer)
            ->build();
    }

    public function getPayload(string $token): stdClass
    {
        return json_decode($this->tokenValidator->splitToken($token)->getPayload());
    }

    public function renewToken(string $token, string $secret, int $minutes): string
    {
        $oldPayload = $this->getPayload($token);

        return $this->getToken($oldPayload->user_id, $oldPayload->user_type, $secret, $minutes, $oldPayload->iss);
    }

    public function validateToken(string $token, string $secret): bool
    {
        return $this->tokenValidator->splitToken($token)
            ->validateExpiration()
            ->validateSignature($secret);
    }
}
