<?php

namespace Tests\Helper;

use Faker\Factory;
use Carbon\Carbon;

class Post
{
    public static function post(bool $withId = false, bool $publish = true): array
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
            'publish' => $publish,
            'publish_at' => $publish ? Carbon::now()->subMinute()->toDateTimeString() : Carbon::now()->addMinute()->toDateTimeString(),
            'created_at' => $faker->date() . ' ' . $faker->time(),
            'updated_at' => $faker->date() . ' ' . $faker->time()
        ];

        if ($withId) {
            $post['id'] = $faker->randomNumber(5) . $faker->word;
        }

        return $post;
    }
}
