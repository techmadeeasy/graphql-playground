<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create multiple users
        $users = \App\Models\User::factory(10)->create();

        // Create categories
        $categories = \App\Models\Category::factory(5)->active()->create();

        // Create tags
        $tags = \App\Models\Tag::factory(15)->create();

        // Create posts with relationships
        $posts = collect();

        foreach ($categories as $category) {
            // Create 8-12 posts per category
            $categoryPosts = \App\Models\Post::factory(rand(8, 12))
                ->published()
                ->create([
                    'category_id' => $category->id,
                    'author_id' => $users->random()->id,
                ]);

            // Add some featured posts
            $categoryPosts->random(2)->each(function ($post) {
                $post->update(['is_featured' => true]);
            });

            // Add some draft posts
            \App\Models\Post::factory(rand(2, 4))
                ->draft()
                ->create([
                    'category_id' => $category->id,
                    'author_id' => $users->random()->id,
                ]);

            $posts = $posts->merge($categoryPosts);
        }

        // Attach random tags to posts
        $posts->each(function ($post) use ($tags) {
            $post->tags()->attach(
                $tags->random(rand(1, 4))->pluck('id')->toArray()
            );
        });

        // Create comments for posts
        $posts->each(function ($post) use ($users) {
            // Create 3-8 comments per post
            $comments = \App\Models\Comment::factory(rand(3, 8))
                ->approved()
                ->create([
                    'post_id' => $post->id,
                    'user_id' => $users->random()->id,
                ]);

            // Create some guest comments
            \App\Models\Comment::factory(rand(1, 3))
                ->guest()
                ->approved()
                ->create([
                    'post_id' => $post->id,
                ]);

            // Create replies to some comments
            $comments->random(min(2, $comments->count()))->each(function ($comment) use ($post, $users) {
                \App\Models\Comment::factory(rand(1, 2))
                    ->approved()
                    ->create([
                        'post_id' => $post->id,
                        'parent_id' => $comment->id,
                        'user_id' => $users->random()->id,
                    ]);
            });

            // Create some pending comments for moderation
            \App\Models\Comment::factory(rand(1, 2))
                ->pending()
                ->create([
                    'post_id' => $post->id,
                    'user_id' => $users->random()->id,
                ]);
        });

        $this->command->info('Blog data seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- 1 User (author@blog.com)');
        $this->command->info('- ' . $categories->count() . ' Categories');
        $this->command->info('- ' . $tags->count() . ' Tags');
        $this->command->info('- ' . \App\Models\Post::count() . ' Posts');
        $this->command->info('- ' . \App\Models\Comment::count() . ' Comments');
    }
}
