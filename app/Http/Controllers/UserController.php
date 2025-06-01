<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        if (Gate::denies('admin-access')) {
            return response('Gate denies: Unauthorized.', 401);
        }

        return Inertia::render('Users/Index', [
            'users' => User::paginate(6),
        ]);
    }

    public function updateProfile(Request $request, User $user)
    {
        $validated = $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $affectedRows = User::where("id", $validated['id'])->update([
            "name" => $validated['name'],
            "email" => $validated['email'],
        ]);

        return response()->json([
            'message' => 'updateProfile successful',
            'affectedRows' => $affectedRows,
        ]);
    }


public function upload(Request $request)
{
    $request->validate([
        'photo' => 'required|image|max:10240',
    ]);

    // Upload bestand
    $path = $request->file('photo')->store('profile-photos', 'public');

    // Oude foto verwijderen (optioneel)
    $user = Auth::user();
    if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
        Storage::disk('public')->delete($user->profile_photo_path);
    }

    // Sla nieuwe pad op in de user-tabel
    $user->profile_photo_path = $path;
    $user->save();

    // Stuur response terug naar frontend
    return response()->json([
        'message' => 'Foto succesvol geüpload en opgeslagen.',
        'filename' => basename($path),
        'path' => asset('storage/' . $path),
    ]);
}

    
}
