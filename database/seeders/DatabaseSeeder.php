<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $memberRole = Role::create(['name'=> 'member']);
        $editorRole = Role::create(['name'=> 'editor']);
        $adminRole = Role::create(['name'=> 'admin']);
        $developerRole = Role::create(['name'=> 'developer']);
        $masterRole = Role::create(['name'=> 'master']);

        // Add the ProviderSeeder
        $this->call(ProviderSeeder::class);

        // Call other seeders
        $this->call([
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
            BusinessServiceSeeder::class,
            FeatureSeeder::class,
            FutureSeeder::class,
            I1TablesDemoSeeder::class,
            I1OrdersSeeder::class
        ]);

        // Get all users and roles
        $users = User::all();
        $roles = Role::all();

        // First, give member role to all users
        foreach ($users as $user) {
            $user->roles()->attach($memberRole);
        }

        // Then, give all roles to lbakker@me.com
        $adminUser = User::where('email', 'lbakker@me.com')->first();
        if ($adminUser) {
            // Detach existing roles first to avoid duplicates
            $adminUser->roles()->detach();
            // Attach all roles
            $adminUser->roles()->attach($roles);
        }

        // Then, give all roles to jawsome.orbit@gmail.com
        $adminUser = User::where('email', 'jawsome.orbit@gmail.com')->first();
        if ($adminUser) {
            // Detach existing roles first to avoid duplicates
            $adminUser->roles()->detach();
            // Attach all roles
            $adminUser->roles()->attach($roles);
        }

        // Add this line after creating roles
        $this->call(PermissionSeeder::class);

        // Create demo team
        $demoTeam = \App\Models\Team::create([
            'name' => 'demo', 
            'id' => 1, 
            'user_id' => 1, 
            'personal_team' => 1
        ]);

        // Set current team for all users
        $users = User::all();
        foreach ($users as $user) {
            $user->update(['current_team_id' => 1]);
        }
    }
}