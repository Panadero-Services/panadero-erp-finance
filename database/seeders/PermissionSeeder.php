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
            'global' => [
                'global-view' => [
                    'description' => 'Can view global',
                    'conditions' => json_encode([])
                ],
                'global-create' => [
                    'description' => 'Can create global',
                    'conditions' => json_encode([])
                ],
                'global-edit' => [
                    'description' => 'Can edit global',
                    'conditions' => json_encode([])
                ],
                'global-delete' => [
                    'description' => 'Can delete global',
                    'conditions' => json_encode([])
                ],
                'canReadAll' => [
                    'description' => 'Full access to read all futures',
                    'conditions' => json_encode([])
                ],
                'canReadUnlocked' => [
                'description' => 'Access to read all unlocked futures',
                'conditions' => json_encode(['respect_lock' => true])
            ],
            'canReadProject' => [
                'description' => 'Access to read all project futures',
                'conditions' => json_encode(['project_based' => true, 'respect_lock' => true])
            ],
            'canReadByStatus' => [
                'description' => 'Access to read specific status futures',
                'conditions' => json_encode(['project_based' => true, 'respect_lock' => true, 'status_allowed' => ['idle', 'in_progress', 'review']])
            ],
            'canReadOwn' => [
                'description' => 'Access to read own futures only',
                'conditions' => json_encode(['owner_only' => true, 'respect_lock' => true, 'status_allowed' => ['completed', 'review']])
            ]
            ],
            'users' => [
                'view-users' => [
                    'description' => 'Can view user list',
                    'conditions' => json_encode([])
                ],
                'create-users' => [
                    'description' => 'Can create new users',
                    'conditions' => json_encode([])
                ],
                'edit-users' => [
                    'description' => 'Can edit user details',
                    'conditions' => json_encode([])
                ],
                'delete-users' => [
                    'description' => 'Can delete users',
                    'conditions' => json_encode([])
                ],
            ],
            'roles' => [
                'view-roles' => [
                    'description' => 'Can view roles',
                    'conditions' => json_encode([])
                ],
                'create-roles' => [
                    'description' => 'Can create new roles',
                    'conditions' => json_encode([])
                ],
                'edit-roles' => [
                    'description' => 'Can edit roles',
                    'conditions' => json_encode([])
                ],
                'delete-roles' => [
                    'description' => 'Can delete roles',
                    'conditions' => json_encode([])
                ],
            ],
            'articles' => [
                'view-articles' => [
                    'description' => 'Can view articles',
                    'conditions' => json_encode([])
                ],
                'create-articles' => [
                    'description' => 'Can create articles',
                    'conditions' => json_encode([])
                ],
                'edit-articles' => [
                    'description' => 'Can edit articles',
                    'conditions' => json_encode([])
                ],
                'delete-articles' => [
                    'description' => 'Can delete articles',
                    'conditions' => json_encode([])
                ],
            ],
            'settings' => [
                'manage-settings' => [
                    'description' => 'Can manage application settings',
                    'conditions' => json_encode([])
                ],
            ],
        ];

        // Create permissions
        foreach ($permissions as $group => $groupPermissions) {
            foreach ($groupPermissions as $name => $config) {
                Permission::create([
                    'name' => $name,
                    'description' => $config['description'],
                    'group' => $group,
                    'conditions' => $config['conditions']
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