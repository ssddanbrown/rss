<?php

namespace Database\Factories;

use App\Models\Feed;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'feed_id' => Feed::factory(),
            'published_at' => now()->subHours(random_int(0, 200))->unix(),
            'title' => $this->faker->title,
            'description' => $this->faker->words(50, true),
            'url' => $this->faker->url . '?query=' . random_int(0, 1000),
            'thumbnail' => ''
        ];
    }
}
