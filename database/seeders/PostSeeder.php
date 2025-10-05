<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::factory()->count(100)->create();
        $posts = Post::all();
        foreach ($posts as $post) {
            $count = fake()->numberBetween(10, 100);
            PostView::factory()
                ->forPost($post)
                ->count($count)
                ->create();
            $post->update(['view_count' => $count]);
        }
    }
}
