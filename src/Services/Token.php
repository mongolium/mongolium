<?php

namespace Mongolium\Services;

use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use Carbon\Carbon;
use Mongolium\Services\Db\Orm;
use Mongolium\Exceptions\TokenException;
use stdClass;

class Token
{
    private $tokenBuilder;

    private $tokenValidator;

    private $orm;

    public function __construct(TokenBuilder $tokenBuilder, TokenValidator $tokenValidator, Orm $orm)
    {
        $this->tokenBuilder = $tokenBuilder;

        $this->tokenValidator = $tokenValidator;

        $this->orm = $orm;
    }

    public function makeToken(string $userId, string $userType, string $secret, int $minutes, string $issuer): string
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

        return $this->makeToken($oldPayload->user_id, $oldPayload->user_type, $secret, $minutes, $oldPayload->iss);
    }

    public function validateToken(string $token, string $secret): bool
    {
        return $this->tokenValidator->splitToken($token)
            ->validateExpiration()
            ->validateSignature($secret);
    }

    public function createToken(string $username, string $password, string $secret, int $minutes, string $issuer): string
    {
        if ($this->orm->count('Mongolium\Model\User', ['username' => $username, 'password' => $password]) === 1) {
            $user = $this->orm->find('Mongolium\Model\User', ['username' => $username, 'password' => $password]);

            $user = $user->extract();

            return $this->makeToken($user['id'], $user['type'], $secret, $minutes, $issuer);
        }

        throw new TokenException('Failed to generate token, please provide valid user credentials');
    }
}
