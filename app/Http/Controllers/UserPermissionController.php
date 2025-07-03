<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class UserPermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        \Log::info('Auth check:', [
            'is_authenticated' => auth()->check(),
            'user' => auth()->user(),
            'session_id' => session()->getId(),
            'session_data' => session()->all()
        ]);

        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthorized',
                'debug' => [
                    'session_present' => session()->has('auth'),
                    'session_id' => session()->getId()
                ]
            ], 401);
        }
        return User::with('roles')->get();
    }

    public function search(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $query = $request->get('q', '');
        return User::with('roles')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->get();
    }

    public function roles(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return Role::with('permissions')->get();
    }

    public function searchRoles(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $query = $request->get('q', '');
        return Role::with('permissions')
            ->where('name', 'like', "%{$query}%")
            ->get();
    }

    public function permissions(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return Permission::all();
    }

    public function updateRoles(Request $request, User $user)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->roles()->sync($request->roles);
        return response()->json(['message' => 'Roles updated successfully']);
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($request->permissions);
        return response()->json(['message' => 'Permissions updated successfully']);
    }
} 