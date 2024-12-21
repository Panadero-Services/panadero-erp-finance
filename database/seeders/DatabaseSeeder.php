<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->withPersonalTeam()->create();

//        User::factory()->withPersonalTeam()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);
    
        $users = User::factory(10)->create();
        $posts = Post::factory(300)->recycle($users)->create();
        $comments = Comment::factory(200)->recycle($users)->recycle($posts)->create();

        $lieuwe = User::factory()
            ->has(Post::factory(45))
            ->has(Comment::factory(120)->recycle($posts))
            ->create([
                'name' => 'lieuwe Bakker',
                'email' => 'lbakker@me.com',
                'current_team_id' => 1,
            ]);
    }
}
