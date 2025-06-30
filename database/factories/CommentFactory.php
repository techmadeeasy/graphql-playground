<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => 1, // Will be overridden when creating with relationships
            'user_id' => $this->faker->boolean(70) ? 1 : null, // 70% chance of being from a registered user
            'author_name' => $this->faker->boolean(30) ? $this->faker->name : null, // 30% chance of guest comment
            'author_email' => $this->faker->boolean(30) ? $this->faker->email : null, // 30% chance of guest comment
            'content' => $this->faker->paragraphs(2, true),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'parent_id' => null, // Will be set for replies
        ];
    }

    /**
     * Indicate that the comment is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the comment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the comment is from a guest user.
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'author_name' => $this->faker->name,
            'author_email' => $this->faker->email,
        ]);
    }
}
