<?php

namespace Tests\Helper;

use Faker\Factory;

class Post
{
    public static function post(bool $withId = false): array
    {
        $faker = Factory::create();

        $title = $faker->unique()->sentence(3);

        $post = [
            'title' => $title,
            'slug' => str_replace(' ', '-', $title),
            'description' => $faker->sentence(10),
            'text' => $faker->paragraphs(3, true),
            'tags' => [$faker->word],
            'creator_id' => $faker->randomNumber(5) . $faker->word,
            'author_id' => $faker->randomNumber(5) . $faker->word,
            'publish' => $faker->boolean,
            'publish_at' => $faker->date() . ' ' . $faker->time(),
            'created_at' => $faker->date() . ' ' . $faker->time(),
            'updated_at' => $faker->date() . ' ' . $faker->time()
        ];

        if ($withId) {
            $post['id'] = $faker->randomNumber(5) . $faker->word;
        }

        return $post;
    }
}
