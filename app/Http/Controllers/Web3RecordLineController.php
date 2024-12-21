<?php

namespace App\Http\Controllers;

use App\Models\Web3RecordLine;
use Illuminate\Http\Request;

class Web3RecordLineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
    CREATE TABLE `web3_record_lines` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `web3record_id` int(11) NOT NULL,
      `line` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
      `line_nr` int(11) NOT NULL,
      `abi` text COLLATE utf8mb4_unicode_ci,
      `parameters` text COLLATE utf8mb4_unicode_ci,
      `value` text COLLATE utf8mb4_unicode_ci,
      `int_value` int(11) DEFAULT NULL,
      `columns` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `type` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `style` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `detail` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `is_active` tinyint(1) NOT NULL,
      `is_live` tinyint(1) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=543 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    */

    public function create(Request $request) {
        //
        $request->validate([
            'line' => 'required|string|max:80',
        ]);

        if($request->web3record_id && $request->line && $request->line_nr) {
            $web3RecordLine = new Web3RecordLine();
            $web3RecordLine->web3record_id = $request->web3record_id;
            $web3RecordLine->line = $request->line;
            $web3RecordLine->line_nr = $request->line_nr;
            $web3RecordLine->abi = $request->abi;
            $web3RecordLine->parameters =333;
            $web3RecordLine->value = $request->value;
            $web3RecordLine->int_value = $request->int_value;
            $web3RecordLine->columns = $request->columns;
            $web3RecordLine->type = $request->type;
            $web3RecordLine->style = $request->style;
            $web3RecordLine->detail = $request->detail;
            $web3RecordLine->is_active = $request->is_active;
            $web3RecordLine->is_live = $request->is_live;        
            $web3RecordLine->save();
            sleep(1);
 //           return "Success!";
        }
   //     return "Not so good!";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'line' => 'required|string|max:80',
        ]);

        if($request->web3record_id && $request->line && $request->line_nr) {
            $web3RecordLine = new Web3RecordLine();
            $web3RecordLine->web3record_id = $request->web3record_id;
            $web3RecordLine->line = $request->line;
            $web3RecordLine->line_nr = $request->line_nr;
            $web3RecordLine->abi = $request->abi;
            $web3RecordLine->parameters =333;
            $web3RecordLine->value = $request->value;
            $web3RecordLine->int_value = $request->int_value;
            $web3RecordLine->columns = $request->columns;
            $web3RecordLine->type = $request->type;
            $web3RecordLine->style = $request->style;
            $web3RecordLine->detail = $request->detail;
            $web3RecordLine->is_active = $request->is_active;
            $web3RecordLine->is_live = $request->is_live;        
            $web3RecordLine->save();
            sleep(1);
            //            return "Success!";
        }
       // return "Not so good!";
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Web3RecordLine  $web3RecordLine
     * @return \Illuminate\Http\Response
     */
    public function show(Web3RecordLine $web3RecordLine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Web3RecordLine  $web3RecordLine
     * @return \Illuminate\Http\Response
     */
    public function edit(Web3RecordLine $web3RecordLine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Web3RecordLine  $web3RecordLine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Web3RecordLine $web3RecordLine)
    {
        $web3RecordLine->columns = $request->columns;
        $web3RecordLine->type = $request->type;
        $web3RecordLine->style = $request->style;
        $web3RecordLine->detail = $request->detail;
        $web3RecordLine->value = $request->value;
        $web3RecordLine->is_active = $request->is_active;
        $web3RecordLine->is_live = $request->is_live;

        if ($request->update_parameters) {
             $web3RecordLine->parameters = $request->parameters;
        }
        if ($request->extended_form) {
             if($request->web3record_id) $web3RecordLine->web3record_id = $request->web3record_id;
             if($request->line_nr) $web3RecordLine->line_nr = $request->line_nr;
             $web3RecordLine->int_value = $request->int_value;
             $web3RecordLine->abi = $request->abi;
        }
        $web3RecordLine->save();
        sleep(1);
  }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Web3RecordLine  $web3RecordLine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Web3RecordLine $web3RecordLine)
    {
        //
    }
}
