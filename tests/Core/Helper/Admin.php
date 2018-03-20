<?php

namespace Tests\Core\Helper;

use Faker\Factory;

class Admin
{
    public static function admin(bool $withId = false): array
    {
        $faker = Factory::create();

        $admin = [
            'username' => $faker->unique()->userName,
            'password' => $faker->randomNumber(5) . $faker->word . $faker->randomNumber(3),
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->unique()->email,
            'type' => $faker->randomElement(['super_admin', 'admin', 'editor']),
            'created_at' => $faker->date() . ' ' . $faker->time(),
            'updated_at' => $faker->date() . ' ' . $faker->time()
        ];

        if ($withId) {
            $admin['id'] = $faker->randomNumber(5) . $faker->word;
        }

        return $admin;
    }
}
