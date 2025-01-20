<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::paginate(6)
        ]);
    }


 public function updateProfile(Request $request, User $user)
    {
        $id = $request->id;
        $name = $request->name;

        $affectedRows = User::where("id", $id)->update(["name" => $name]);
        sleep(1);

        return $affectedRows;
    }




}
