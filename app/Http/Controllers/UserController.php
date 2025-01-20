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


 public function update(Request $request, User $user)
    {


        sleep(1);

        return $request;

    }




}
