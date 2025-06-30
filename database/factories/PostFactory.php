<?php

namespace Database\Factories;

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
    public function definition(): array
    {
        $title = $this->faker->sentence(6);
        
        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 1000),
            'excerpt' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(8, true),
            'featured_image' => $this->faker->imageUrl(800, 400, 'technology'),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'author_id' => 1, // Will be overridden when creating with relationships
            'views' => $this->faker->numberBetween(0, 10000),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the post is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
