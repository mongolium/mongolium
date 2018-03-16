<?php

namespace Tests\Helper;

use Faker\Factory;

class Page
{
    public static function page(bool $withId = false): array
    {
        $faker = Factory::create();

        $admin = [
            'title' => $faker->unique()->userName,
            'slug' => str_replace(' ', '-', $faker->randomNumber(5) . $faker->word . $faker->randomNumber(3)),
            'description' => $faker->firstName,
            'text' => $faker->lastName,
            'tags' => [$faker->word, $faker->word],
            'creator_id' => $faker->randomNumber(5) . $faker->word,
            'created_at' => $faker->date() . ' ' . $faker->time(),
            'updated_at' => $faker->date() . ' ' . $faker->time()
        ];

        if ($withId) {
            $admin['id'] = $faker->randomNumber(5) . $faker->word;
        }

        return $admin;
    }
}
