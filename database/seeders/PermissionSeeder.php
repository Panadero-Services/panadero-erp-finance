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
                 
                'globalRead' => [
                    'description' => 'Full access to read all records',
                    'conditions' => json_encode([])
                ],
                'globalEdit' => [
                    'description' => 'Full access to edit all records',
                    'conditions' => json_encode([])
                ],   
                'globalDelete' => [
                    'description' => 'Full access to delete all records',
                    'conditions' => json_encode([])
                ],   

                'canReadUnlocked' => [
                    'description' => 'Access to read all unlocked records',
                    'conditions' => json_encode(['respect_lock' => true])
                ],
                'canReadProject' => [
                    'description' => 'Access to read all project records',
                    'conditions' => json_encode(['project_based' => true, 'respect_lock' => true])
                ],
                'canReadByStatus' => [
                    'description' => 'Access to read specific status records',
                    'conditions' => json_encode(['project_based' => true, 'respect_lock' => true, 'status_allowed' => ['idle', 'in_progress', 'review']])
                ],
                'canReadOwn' => [
                    'description' => 'Access to read own records only',
                    'conditions' => json_encode(['owner_only' => true, 'respect_lock' => true, 'status_allowed' => ['completed', 'review']])
                ]
            ],
            'users' => [
                'canManageUsers' => [
                    'description' => 'Full access to manage all users',
                    'conditions' => json_encode([])
                ]
            ],
            'settings' => [
                'canManageSettings' => [
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
                            $p->name === 'viewUsers'
                        )
                    );
                    break;

                case 'member':
                    // Member gets view permissions only
                    $role->permissions()->sync(
                        $allPermissions->filter(fn($p) => 
                            str_starts_with($p->name, 'view')
                        )
                    );
                    break;
            }
        }
    }
} 