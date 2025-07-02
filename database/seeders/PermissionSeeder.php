<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define default permissions by group
        $permissions = [
            'users' => [
                'view-users' => 'Can view user list',
                'create-users' => 'Can create new users',
                'edit-users' => 'Can edit user details',
                'delete-users' => 'Can delete users',
            ],
            'roles' => [
                'view-roles' => 'Can view roles',
                'create-roles' => 'Can create new roles',
                'edit-roles' => 'Can edit roles',
                'delete-roles' => 'Can delete roles',
            ],
            'articles' => [
                'view-articles' => 'Can view articles',
                'create-articles' => 'Can create articles',
                'edit-articles' => 'Can edit articles',
                'delete-articles' => 'Can delete articles',
            ],
            'settings' => [
                'manage-settings' => 'Can manage application settings',
            ],
        ];

        // Create permissions
        foreach ($permissions as $group => $groupPermissions) {
            foreach ($groupPermissions as $name => $description) {
                Permission::create([
                    'name' => $name,
                    'description' => $description,
                    'group' => $group,
                ]);
            }
        }

        // Assign permissions to roles
        $roles = Role::all();
        $allPermissions = Permission::all();

        foreach ($roles as $role) {
            switch ($role->name) {
                case 'master':
                    // Master gets all permissions
                    $role->permissions()->sync($allPermissions);
                    break;

                case 'admin':
                    // Admin gets all except role management
                    $role->permissions()->sync(
                        $allPermissions->filter(fn($p) => $p->group !== 'roles')
                    );
                    break;

                case 'editor':
                    // Editor gets article management and view permissions
                    $role->permissions()->sync(
                        $allPermissions->filter(fn($p) => 
                            $p->group === 'articles' || 
                            $p->name === 'view-users'
                        )
                    );
                    break;

                case 'member':
                    // Member gets view permissions only
                    $role->permissions()->sync(
                        $allPermissions->filter(fn($p) => 
                            str_starts_with($p->name, 'view-')
                        )
                    );
                    break;
            }
        }
    }
} 