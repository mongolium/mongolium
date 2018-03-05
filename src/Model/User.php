<?php

namespace Helium\Model;

use Helium\Services\Db\BaseModel;
use Helium\Services\Db\Hydrator;

class User extends BaseModel
{
    private $username;

    private $password;

    private $type;

    protected static $table = 'users';

    protected static $unique = ['username'];

    private function __construct(string $id, string $username, string $password, string $type)
    {
        $this->id = $id;

        $this->username = $username;

        $this->password = $password;

        $this->type = $type;
    }

    public static function hydrate(array $data): Hydrator
    {
        return new self(
            $data['id'],
            $data['username'],
            $data['password'],
            $data['type']
        );
    }

    public function extract(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'type' => $this->type
        ];
    }
}
