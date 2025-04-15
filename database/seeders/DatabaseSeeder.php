<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Role;

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


/*
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
*/
    
    $memberRole = Role::create(['name'=> 'member']);
    $editorRole = Role::create(['name'=> 'editor']);
    $adminRole = Role::create(['name'=> 'admin']);
    $developerRole = Role::create(['name'=> 'developer']);
    $masterRole = Role::create(['name'=> 'master']);

    $memberUser = User::factory()
                ->create([
                    'name' => 'Member',
                    'email' => 'member@example.com'
                ]);

    $memberUser->roles()->attach($memberRole);


    $editorUser = User::factory()
                ->create([
                    'name' => 'Editor',
                    'email' => 'editor@example.com'
                ]);
                
    $editorUser->roles()->attach($editorRole);

    $adminUser = User::factory()
                ->create([
                    'name' => 'Admin',
                    'email' => 'admin@example.com'
                ]);
                
    $adminUser->roles()->attach($adminRole);


    // this user is assigned both 2 roles
    $memberEditorUser = User::factory()
                ->create([
                    'name' => 'Member/Editor',
                    'email' => 'me@example.com'
                ]);
                
    $memberEditorUser->roles()->attach($memberRole);
    $memberEditorUser->roles()->attach($editorRole);

    $posts = Post::factory(10)->recycle($editorUser)->create();
    $posts = Post::factory(10)->recycle($memberEditorUser)->create();


    }
}
