<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(rand(3, 6));

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->optional(0.8)->sentence(),
            'body' => implode("\n\n", fake()->paragraphs(rand(3, 6))),
            'featured_image' => null,
            'is_published' => fake()->boolean(70),
            'published_at' => fn (array $attributes) => $attributes['is_published']
                ? fake()->dateTimeBetween('-1 year')
                : null,
        ];
    }

    /**
     * A published post.
     */
    public function published(): static
    {
        return $this->state(fn () => [
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * A draft post.
     */
    public function draft(): static
    {
        return $this->state(fn () => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
