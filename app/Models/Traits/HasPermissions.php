<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait HasPermissions
{
    /**
     * Define permission-based access for the model
     * Override this method in each model to define specific permissions
     */
    public static function getPermissionAccess(): array
    {
        $modelName = class_basename(static::class);
        $tableName = (new static)->getTable();
        
        return [
            'canReadAll' => [
                'description' => "Full access to read all {$tableName}",
                'conditions' => [] // No conditions means full access
            ],
            'canReadUnlocked' => [
                'description' => "Access to read all unlocked {$tableName}",
                'conditions' => [
                    'respect_lock' => true
                ]
            ],
         'canReadProject' => [
               'description' => 'Access to read all project records',
               'conditions' => json_encode(['project_based' => true, 'respect_lock' => true])
           ],
            'canReadByStatus' => [
                'description' => "Access to read specific status {$tableName}",
                'conditions' => [
                    'project_based' => true,
                    'respect_lock' => true,
                    'status_allowed' => ['idle', 'in_progress', 'review']
                ]
            ],
            'canReadOwn' => [
                'description' => "Access to read own {$tableName} only",
                'conditions' => [
                    'owner_only' => true,
                    'respect_lock' => true,
                    'status_allowed' => ['completed', 'review']
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
    }

    /**
     * Scope a query to only include records the user can read based on their permissions
     */
    public function scopeReadable(Builder $query, $userPermissions = null): Builder
    {
        $user = Auth::user();
        
        if (!$user) {
            return $query->where('id', 0);
        }

        if (!$userPermissions) {
            $userPermissions = $user->roles()
                ->with('permissions')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('name')
                ->unique();
        }

        // Check for full access permission
        if ($userPermissions->contains('canReadAll')) {
            return $query;
        }

        // Get available permissions for this model
        $availablePermissions = static::getPermissionAccess();
        
        // Filter user permissions to only include those available for this model
        $relevantPermissions = $userPermissions->intersect(array_keys($availablePermissions));
        
        // If no relevant permissions found, return empty result
        if ($relevantPermissions->isEmpty()) {
            return $query->where('id', 0);
        }

        return $query->where(function($q) use ($relevantPermissions, $user, $availablePermissions) {
            foreach ($relevantPermissions as $permission) {
                $permissionAccess = $availablePermissions[$permission] ?? null;
                if ($permissionAccess) {
                    $conditions = $permissionAccess['conditions'];
                    
                    $q->orWhere(function($subQ) use ($conditions, $user) {
                        // Owner-only condition
                        if ($conditions['owner_only'] ?? false) {
                            $subQ->where('user_id', $user->id);
                        }
                        
                        // Lock respect condition
                        if ($conditions['respect_lock'] ?? false) {
                            $subQ->where(function ($lockQ) use ($user) {
                                $lockQ->where('is_locked', false)
                                     ->orWhere('user_id', $user->id);
                            });
                        }
                        
                        // Status-based filtering
                        if ($conditions['status_allowed'] ?? false) {
                            $subQ->whereIn('status', $conditions['status_allowed']);
                        }
                        
                        // Project-based filtering
                        if ($conditions['project_based'] ?? false) {
                            $subQ->whereHas('project', function($projectQ) use ($user) {
                                $projectQ->where('user_id', $user->id);
                            });
                        }
                        
                        // Active status filtering
                        if ($conditions['active_only'] ?? false) {
                            $subQ->where('is_active', true);
                        }
                        
                        // Published status filtering
                        if ($conditions['published_only'] ?? false) {
                            $subQ->where('is_published', true);
                        }
                    });
                }
            }
        });
    }

    /**
     * Check if user can read this specific record
     */
    public function canBeReadBy($user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        $userPermissions = $user->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique();

        // Full access check
        if ($userPermissions->contains('canReadAll')) {
            return true;
        }

        // Get available permissions for this model
        $availablePermissions = static::getPermissionAccess();
        
        // Filter user permissions to only include those available for this model
        $relevantPermissions = $userPermissions->intersect(array_keys($availablePermissions));
        
        // If no relevant permissions found, deny access
        if ($relevantPermissions->isEmpty()) {
            return false;
        }

        // Check each permission
        foreach ($relevantPermissions as $permission) {
            $permissionAccess = $availablePermissions[$permission] ?? null;
            if ($permissionAccess) {
                $conditions = $permissionAccess['conditions'];
                
                $canRead = true;
                
                // Owner check
                if ($conditions['owner_only'] ?? false) {
                    $canRead = $canRead && $this->user_id == $user->id;
                }
                
                // Lock check
                if ($conditions['respect_lock'] ?? false) {
                    $canRead = $canRead && (!$this->is_locked || $this->user_id == $user->id);
                }
                
                // Status check
                if ($conditions['status_allowed'] ?? false) {
                    $canRead = $canRead && in_array($this->status, $conditions['status_allowed']);
                }
                
                // Active check
                if ($conditions['active_only'] ?? false) {
                    $canRead = $canRead && $this->is_active;
                }
                
                // Published check
                if ($conditions['published_only'] ?? false) {
                    $canRead = $canRead && $this->is_published;
                }
                
                if ($canRead) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user can edit this specific record
     */
    public function canBeEditedBy($user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Check for global edit permission
        if ($user->hasPermission('globalEdit')) {
            return true;
        }

        // Check for model-specific edit permission
        $tableName = $this->getTable();
        $editPermission = "edit" . Str::studly(Str::singular($tableName));
        if ($user->hasPermission($editPermission)) {
            return true;
        }

        // Owner can edit their own records
        return $this->user_id == $user->id;
    }

    /**
     * Check if user can delete this specific record
     */
    public function canBeDeletedBy($user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Check for global delete permission
        if ($user->hasPermission('globalDelete')) {
            return true;
        }

        // Check for model-specific delete permission
        $tableName = $this->getTable();
        $deletePermission = "delete" . Str::studly(Str::singular($tableName));
        if ($user->hasPermission($deletePermission)) {
            return true;
        }

        // Owner can delete their own records
        return $this->user_id == $user->id;
    }
} 