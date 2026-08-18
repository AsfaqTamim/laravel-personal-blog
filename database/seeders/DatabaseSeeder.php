<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@dml-blog.test'],
            [
                'name' => 'Clara Dawson',
                'password' => Hash::make('password'),
            ]
        );

        foreach (['Laravel', 'Tutorials', 'News', 'Tips & Tricks'] as $name) {
            Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
        $categories = Category::all();

        if (Post::count() === 0) {
            Post::factory()->count(5)->published()->create(['user_id' => $admin->id]);
            Post::factory()->count(2)->draft()->create(['user_id' => $admin->id]);
        }

        // Attach categories to posts that have none
        Post::with('categories')->get()->each(function (Post $post) use ($categories) {
            if ($post->categories->isEmpty()) {
                $post->categories()->attach(
                    $categories->random(min(rand(1, 2), $categories->count()))->pluck('id')
                );
            }
        });

        if (Comment::count() === 0) {
            $publishedIds = Post::where('is_published', true)->pluck('id');

            if ($publishedIds->isNotEmpty()) {
                Comment::factory()->count(10)->create([
                    'post_id' => fn () => $publishedIds->random(),
                ]);
            }
        }
    }
}
