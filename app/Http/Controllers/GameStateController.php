<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\GameStateUpdated;

class GameStateController extends Controller
{
    public function update(Request $request)
    {
        $state = $request->validate([
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'rotation' => 'required|numeric',
            'score' => 'required|numeric',
            'thrust' => 'required|boolean'
        ]);

        broadcast(new GameStateUpdated(
            $request->user()->id,
            $state
        ))->toOthers();

        return response()->noContent();
    }
} 