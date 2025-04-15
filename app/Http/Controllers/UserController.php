<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {

        if (Gate::denies('admin-access')){
            return response('Gate denies: UnAuthorized.',401);
        }

        return Inertia::render('Users/Index', [
            'users' => User::paginate(6)
        ]);
    }

 public function updateProfile(Request $request, User $user)
    {
        $id = $request->id;
        $name = $request->name;
        $email = $request->email;
        $json = $request->json;

        $affectedRows = User::where("id", $id)->update([
            "name" => $name,
            "email" => $email,
            "json" => $json
        ]);
        sleep(1);
        return "updateProfile successfull: ".$affectedRows;
//        return $affectedRows;
    }




}
