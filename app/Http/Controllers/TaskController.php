<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();

        return Inertia::render('Tasks/Index',[
            'tasks' => $tasks
 //          'tags' => Tag::paginate()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render(
            'Tasks/Create'
        );
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
        $create = Task::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $request->user_id,
            'tag_id' => $request->tag_id,
            'completed' => $request->completed
        ]);



       // sleep(1);
        //return ;//$create->id;

       // return redirect()->route('task.index')->with('message', 'Tag Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        return Inertia::render(
            'Tasks/Edit',
            [
                'task' => $task
            ]
        );
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:80',
        ]);
        $task->title = $request->title;
        $task->content = $request->content;
        $task->save();
        sleep(1);

        //return redirect()->route('tasks.index')->with('message', 'Task Updated Successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

      //  sleep(1);
      //  return redirect()->route('tasks.index')->with('message', 'Task Deleted Successfully');
        return ;

    }
}
