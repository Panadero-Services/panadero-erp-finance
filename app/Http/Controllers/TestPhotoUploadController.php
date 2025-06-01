<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestPhotoUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'], // max 2MB
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos');

            return back()->with('success', 'Photo uploaded successfully: ' . $path);
        }

        return back()->withErrors(['photo' => 'No photo was uploaded.']);
    }
}

