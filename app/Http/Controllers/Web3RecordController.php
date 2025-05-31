<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

use App\Models\Team;
use App\Models\Project;

use App\Models\Web3Record;
use App\Models\Web3Type;
use App\Models\Web3Project;
use App\Models\Web3Chain;
use App\Models\Web3RecordLine;

class Web3RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $web3records = Web3Record::all();

        //return Inertia::render('Web3Records/Index',[
        //    'web3Record' => $web3records
        //]);
    }

    /** api Calls 
    * client: Web3DbStore.js
    * Dynamically load web3Record
    *
    * @return \Illuminate\Http\Response
    */
    public function getrecord(Request $request)
    {
        // default API CALL
        $caller =   $request->caller;       // this is the client
        $provider = $request->provider;     // this is the model
        $field =    $request->field;        // this is the column
        $search =   $request->search;       // this is the value to look for 
        
        $r=(object)NULL;
        // check Valid Provider (model) calles
        $model = 'App\Models\\'.$provider;

        if ($provider == 'Web3Record') $r = $model::orderBy("id", "desc")->with('web3type')->with('web3project')->with('web3chain')->with('web3recordlines')->where($field,$search)->get();

        return response()->json($r);
    }

    /** api Calls 
    * client: Web3DbStore.js
    */
    public function getrecords(Request $request)
    {
        // default API CALL
        $caller = $request->caller;
        $provider = $request->provider;
        $user = $request->user;
        $key = $request->key;
        $active = true;
        
        $r=(object)NULL;
        // check Valid Provider (model) calls
        $model = 'App\Models\\'.$provider;
        if ($provider == 'Team') $r = $model::select(['id', 'name', 'user_id'])->orderBy("id", "desc")->where('id','>' ,0)->get();
        if ($provider == 'Project') $r = $model::select(['id', 'title', 'user_id'])->orderBy("id", "desc")->where('id','>' ,0)->get();
    
        return response()->json($r);
    }

    /** api Calls 
    * client: Web3DbStore.js
    */
    public function getweb3records(Request $request)
    {
        // default API CALL
        $caller = $request->caller;
        $provider = $request->provider;
        $user = $request->user;
        $key = $request->key;
        $active = true;
        
        $r=(object)NULL;
        // check Valid Provider (model) calls
        $model = 'App\Models\\'.$provider;
        if ($provider == 'Web3Record') $r = Web3Record::select(['id','title', 'address', 'web3type_id', 'web3project_id', 'web3chain_id', 'user_id' ,'description', 'url', 'tags', 'is_active', 'is_live', 'completed', 'archived'])->orderBy("id", "desc")->with('web3type')->with('web3project')->with('web3chain')->with('web3recordlines')->where('is_active',1)->get();
        if (in_array($provider,['Web3Type','Web3Project']))$r = $model::select(['id', 'title', 'color'])->orderBy("id", "desc")->where('id','>' ,0)->get();
        if ($provider == 'Web3Chain') $r = $model::select(['id', 'title', 'chain_nr'])->orderBy("id", "desc")->where('id','>' ,0)->get();
 
        return response()->json($r);
    }

    /** api Calls 
     * client: Web3DbStore.js
     * update or insert new web3Record from dashboard2.Web3section.Web3FVorm.vue
    */
    public function setweb3recordline(Request $request)
    {
        $_parameters = $request->parameters;
        if ($_parameters=='0') $_parameters='';

        $record = Web3RecordLine::updateOrInsert(
                ['web3record_id' =>  $request->web3record_id, 'line' => $request->line, 'line_nr' => $request->line_nr ],
                ['abi' => $request->abi, 
                'parameters' => $_parameters,
                'value' => $request->value, 
                'int_value' => $request->int_value, 
                'columns' => $request->columns, 
                'style' => $request->style, 
                'type' => $request->type, 
                'detail' => $request->detail, 
                'is_active' => $request->is_active, 
                'is_live' => $request->is_live 
            ]
        );
        return 'web3RecordLine updated';
    }

/*
    public function setweb3recordline(Request $request)
    {
        $record = Web3RecordLine::updateOrInsert(
                ['web3record_id' =>  $request->web3record_id, 'line' => $request->line, 'line_nr' => $request->line_nr ],
                ['abi' => $request->abi, 
                'parameters' => $request->parameters, 
                'value' => $request->value, 
                'int_value' => $request->int_value, 
                'columns' => $request->columns, 
                'style' => $request->style, 
                'type' => $request->type, 
                'detail' => $request->detail, 
                'is_active' => $request->is_active, 
                'is_live' => $request->is_live 
            ]
        );
        return 'web3RecordLine updated';
    }

*/

    /** api Calls 
    * client: Web3DbStore.js
    * Dynamically load web3RecordLine
    *
    * @return \Illuminate\Http\Response
    */
    public function getweb3recordline(Request $request)
    {
        // default API CALL
        $caller =   $request->caller;       // this is the client
        $provider = $request->model;     // this is the model
        $web3record_id =    $request->web3record_id;        // this is the 1st column.. to look for..
        $line =    $request->line;                          // this is the 2nd column
        $line_nr =    $request->line_nr;                    // this is the 3rd column
        
        $r=(object)NULL;
        // check Valid Provider (model) calles
        // $model = 'App\Models\\'.$provider;
        // if ($provider == 'Web3RecordLine') $response = $model::orderBy("id", "desc")->where('web3record_id',$web3record_id)->where('line',$line)->where('line_nr', $line_nr)->get();
        $r = Web3RecordLine::where('web3record_id',$web3record_id)->where('line',$line)->where('line_nr', $line_nr)->first();
        return response()->json($r);
    }

    /** api Calls 
     * client: Web3DbStore.js
     * update or insert new web3RecordLine from dashboard4.StakePoolSection.vue
    */
    public function setweb3record(Request $request)
    {
        $record = Web3Record::updateOrInsert(
                ['title' =>  $request->title],
                ['web3type_id' => $request->web3type_id, 
                'web3project_id' => $request->web3project_id, 
                'web3chain_id' => $request->web3chain_id, 
                'tags' => $request->tags, 
                'address' => $request->address, 
                'url' => $request->url, 
                'abi' => $request->abi, 
                'description' => $request->description, 
                'json' => $request->json,
                'user_id' => $request->user_id
            ]
        );
        return 'updated';
    }

    public function setweb3recordcomplete(Request $request)
    {
        if ($request->field=="completed") {
            $record = Web3Record::updateOrInsert(
                    ['id' =>  $request->id],
                    ['completed' => $request->completed,
                    'archived' => false
                    ]
            );
        }

        if ($request->field=="archived") {
            $record = Web3Record::updateOrInsert(
                    ['id' =>  $request->id],
                    ['archived' => $request->completed]
            );
        }
        return 'updated';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //return Inertia::render(
        //'Web3Records/Create'
        //);
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
            'title' => 'required|string|max:80',
        ]);
        $create = Web3Record::updateOrInsert(
            ['title' =>  $request->title],
            ['web3type_id' => $request->web3type_id,
            'web3project_id' => $request->web3project_id,
            'web3chain_id' => $request->web3chain_id,
            'tags' => $request->tags,
            'address' => $request->address,
            'url' => $request->url,
            'abi' => $request->abi,
            'description' => $request->description,
            'json' => $request->json,
            'user_id' => $request->user_id
        ]);



       // sleep(1);
        //return ;//$create->id;

       // return redirect()->route('web3record.index')->with('message', 'Web3Record Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Web3Record  $web3record
     * @return \Illuminate\Http\Response
     */
    public function show(Web3Record $web3record)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Web3Record  $web3record
     * @return \Illuminate\Http\Response
     */
    public function edit(Web3Record $web3record)
    {
        return Inertia::render(
            'Web3Record/Edit',
            [
                'web3record' => $web3record
            ]
        );
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Web3Record  $web3record
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Web3Record $web3record)
    {
        $request->validate([
            'title' => 'required|string|max:80',
        ]);
        $web3record->title = $request->title;
        $web3record->web3type_id = $request->web3type_id;
        $web3record->web3project_id = $request->web3project_id;
        $web3record->web3chain_id = $request->web3chain_id;
        $web3record->tags = $request->tags;
        $web3record->address = $request->address;
        $web3record->url = $request->url;
        $web3record->abi = $request->abi;
        $web3record->description = $request->description;
        $web3record->json = $request->json;
        $web3record->user_id = $request->user_id;
        $web3record->completed = $request->completed;
        $web3record->archived = $request->archived;
        $web3record->save();
        sleep(1);

        //return redirect()->route('tasks.index')->with('message', 'Web3Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $web3record
     * @return \Illuminate\Http\Response
     */
    public function destroy(Web3Record $web3record)
    {
        $web3record->delete();

      //  sleep(1);
      //  return redirect()->route('tasks.index')->with('message', 'Web3Record Deleted Successfully');
        return ;

    }

/**
     * Create a new team.
     * manually created for 
     *
     */
    public function insertTeam(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:6',
            'name' => 'required|string|max:18'
        ]);
        $create = Team::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'personal_team' => $request->personal_team
        ]);
        return $request;
    }

/**
     * Create a new project.
     * manually created for 
     *
     */
    public function insertProject(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:6',
            'title' => 'required|string|max:18'
        ]);
        $create = Project::create([
            'title' => $request->title,
            'body' => $request->description,
            'json' => $request->json,
            'user_id' => $request->user_id,
            'is_active' => $request->is_active
        ]);
        return $request;

    }
}
