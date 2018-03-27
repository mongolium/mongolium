<?php

namespace Mongolium\Core\Model;

use Mongolium\Core\Services\Db\BaseModel;
use Mongolium\Core\Services\Db\Hydrator;

class Admin extends BaseModel
{
    protected $username;

    protected $password;

    protected $email;

    protected $firstName;

    protected $lastName;

    protected $type;

    protected $createdAt;

    protected $updatedAt;

    protected static $table = 'admins';

    protected static $unique = ['username'];

    protected $hide = ['password', 'email'];

    protected function __construct(
        string $id,
        string $username,
        string $password,
        string $email,
        string $firstName,
        string $lastName,
        string $type,
        string $createdAt,
        string $updatedAt
    ) {
        $this->id = $id;

        $this->username = $username;

        $this->password = $password;

        $this->email = $email;

        $this->firstName = $firstName;

        $this->lastName = $lastName;

        $this->type = $type;

        $this->createdAt = $createdAt;

        $this->updatedAt = $updatedAt;
    }

    public static function hydrate(array $data): Hydrator
    {
        return new self(
            $data['id'],
            $data['username'],
            $data['password'],
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['type'],
            $data['created_at'],
            $data['updated_at']
        );
    }

    public function extract(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'type' => $this->type,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
