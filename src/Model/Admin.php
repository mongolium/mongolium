<?php

namespace Mongolium\Model;

use Mongolium\Services\Db\BaseModel;
use Mongolium\Services\Db\Hydrator;

class Admin extends BaseModel
{
    private $username;

    private $password;

    private $type;

    protected static $table = 'admins';

    protected static $unique = ['username'];

    protected $hide = ['password'];

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
