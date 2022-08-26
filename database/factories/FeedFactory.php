<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feed>
 */
class FeedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $url = $this->faker->url . '?query=' . random_int(0, 1000);
        return [
            'url' => $url,
            'last_fetched_at' => now()->subHours(random_int(0, 100))->unix(),
            'last_accessed_at' => time(),
        ];
    }
}
