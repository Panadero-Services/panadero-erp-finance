<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Http\Requests\StoreScoreRequest;
use App\Http\Requests\UpdateScoreRequest;


use Illuminate\Support\Facades\Http;

class ScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        //
        return Score::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Score $score)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Score $score)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScoreRequest $request, Score $score)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Score $score)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function setscore(Request $request)
    {
        $request->validate([
            'self' => 'required|string|max:80',
        ]);

//        return $request;

        $create = Score::insert(
            ['self' =>  $request->self,
            'provider' => $request->provider,
            'score' => $request->score,
            'stage' => $request->stage,
            'bonus' => $request->bonus,
            'nft_earned' => $request->nft_earned,
            'json' => $request->json
            ]);

        return $create;

       // sleep(1);
    }

}
