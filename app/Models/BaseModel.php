<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasPermissions;

abstract class BaseModel extends Model
{
    use HasPermissions;

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        return $this->table ?? strtolower(class_basename($this)) . 's';
    }

    /**
     * Get the permission access configuration for this model.
     * Override this in each model to define specific permissions.
     */
    public static function getPermissionAccess(): array
    {
        $tableName = (new static)->getTable();
        
        return [
            'canReadAll' => [
                'description' => "Full access to read all {$tableName}",
                'conditions' => []
            ],
            'canReadOwn' => [
                'description' => "Access to read own {$tableName}",
                'conditions' => [
                    'owner_only' => true,
                    'respect_lock' => true
                ]
            ]
        ];
    }
} 