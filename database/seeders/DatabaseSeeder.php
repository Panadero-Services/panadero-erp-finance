<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Role;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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

    // Comment out user creation to avoid duplicates
    // $memberUser = User::factory()
    //             ->create([
    //                 'name' => 'Member',
    //                 'email' => 'member@example.com'
    //             ]);

    // $memberUser->roles()->attach($memberRole);

    // $editorUser = User::factory()
    //             ->create([
    //                 'name' => 'Editor',
    //                 'email' => 'editor@example.com'
    //             ]);
                
    // $editorUser->roles()->attach($editorRole);

//    $user = User::factory()->create(['id' => 1, 'name' => 'Admin', 'email' => 'admin@example.com']);
                
    // $adminUser->roles()->attach($adminRole);

    // $memberEditorUser = User::factory()
    //             ->create([
    //                 'name' => 'Member/Editor',
    //                 'email' => 'me@example.com'
    //             ]);
                
    // $memberEditorUser->roles()->attach($memberRole);
    // $memberEditorUser->roles()->attach($editorRole);

    // Comment out post creation to avoid using undefined variables
    // $posts = Post::factory(10)->recycle($editorUser)->create();
    // $posts = Post::factory(10)->recycle($memberEditorUser)->create();

    // Add the ProviderSeeder
    $this->call(ProviderSeeder::class);

    $this->call([
        // Comment out the UserSeeder to avoid duplicate users
        UserSeeder::class,
        CategorySeeder::class,
        PostTypeSeeder::class,
        TagSeeder::class,
        PostSeeder::class,
        Web3ChainSeeder::class,
        Web3ProjectSeeder::class,
        Web3TypeSeeder::class,
        Web3RecordSeeder::class,
        ProjectSeeder::class,
        PageSeeder::class,
        UpdatePagesSeeder::class,
        SectionsSeeder::class,
        StateDatasetSeeder::class,
    ]);

    $users = User::all();
    $roles = Role::all();

    foreach ($users as $user) {
        $user->roles()->attach($roles);
    }

    $demoTeam = \App\Models\Team::create(['name' => 'demo', 'id' => 1, 'user_id' => 1, 'personal_team' => 1]);

    $users = User::all();
    foreach ($users as $user) {
        $user->update(['current_team_id' => 1]);
    }
    }
}
