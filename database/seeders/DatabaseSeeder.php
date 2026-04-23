<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = Category::create(['name' => 'Laravel']);
        $categories = Category::create(['name' => 'PHP']);
        $categories = Category::create(['name' => 'DevOps']);
        $categories = Category::create(['name' => 'JavaScript']);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Article::create(['title' => 'Laravel Basics', 'content' => 'Content', 'status' => 'published', 'category_id' => 1, 'user_id' => $user->id, 'published_at' => now()]);
        Article::create(['title' => 'PHP Tips', 'content' => 'Content', 'status' => 'published', 'category_id' => 2, 'user_id' => $user->id, 'published_at' => now()]);
        Article::create(['title' => 'DevOps Guide', 'content' => 'Content', 'status' => 'draft', 'category_id' => 3, 'user_id' => $user->id]);
        Article::create(['title' => 'JS Fundamentals', 'content' => 'Content', 'status' => 'published', 'category_id' => 4, 'user_id' => $user->id, 'published_at' => now()]);
        Article::create(['title' => 'Laravel Advanced', 'content' => 'Content', 'status' => 'draft', 'category_id' => 1, 'user_id' => $user->id]);
        Article::create(['title' => 'DevOps CI/CD', 'content' => 'Content', 'status' => 'published', 'category_id' => 3, 'user_id' => $user->id, 'published_at' => now()]);
    }
}
