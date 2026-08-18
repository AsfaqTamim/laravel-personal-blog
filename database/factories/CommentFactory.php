<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'body' => fake()->sentence(rand(5, 15)),
            'is_approved' => fake()->boolean(70),
        ];
    }

    /**
     * An approved comment.
     */
    public function approved(): static
    {
        return $this->state(fn () => ['is_approved' => true]);
    }

    /**
     * A pending comment.
     */
    public function pending(): static
    {
        return $this->state(fn () => ['is_approved' => false]);
    }
}
