<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Price::all();
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Price $price)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Price $price)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Price $price)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Price $price)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function get(Request $request)
    {
        //

        $r=(object)NULL;
        // check Valid Provider (model) calles
        // $model = 'App\Models\\'.$provider;
        // if ($provider == 'Web3RecordLine') $response = $model::orderBy("id", "desc")->where('web3record_id',$web3record_id)->where('line',$line)->where('line_nr', $line_nr)->get();
        $r = Price::where('token', $request->token)->first();
        //return json($r->price);
        if ($r != NULL) return $r->price;
        return 0;
    }



}
