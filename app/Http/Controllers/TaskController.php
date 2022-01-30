<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display aLL Task.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date = $request->query('date') ?? null;

        $tasks = Task::select('id', 'name', 'description', 'date', 'is_done');

        if($date)
            $tasks = $tasks->where('date', $date);

        $tasks = $tasks->get();

        return response()->json(array('data' => $tasks));
    }

    /**
     * Create Task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->description);
        $validated = $request->validate([
            'name' => 'required|max:25|unique:tasks,name',
            'description' => 'max:255',
            'date' => 'date_format:Y-m-d',
        ]);

        try {
            $task = Task::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'date' => $validated['date']
            ]);

            return response()->json(array('message' => 'Task created successfully.', 'data' => $task), 201);
        }
        catch (Exception $e)
        {
            return response()->json(array('message' =>'Internal server error.'), 500);
        }
    }

    /**
     * Get task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show($task_id)
    {
        try {

            $task = Task::select('id', 'name', 'description', 'date')
                ->where('id', $task_id)->first();

            if (!$task)
                return response()->json(array('message' => 'Task not found.'), 404);

            return response()->json(array('data' => $task), 200);
        } catch (Exception $e) {
            return response()->json(array('message' => 'Internal server error.'), 500);
        }
    }

    /**
     * Update task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $task_id)
    {
        $validated = $request->validate([
            'name' => 'required|max:25|unique:tasks,name,' . $task_id,
            'description' => 'max:255',
            'date' => 'date_format:Y-m-d'
        ]);

        try {
            $task = Task::select('id', 'name', 'description', 'date')
                        ->where('id', $task_id)->first();

            if(!$task)
                return response()->json(array('message' => 'Task not found.'), 404);

            $task->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'date' => $validated['date']
            ]);

            return response()->json(array('message' => 'Task update successfully.'), 200);
        } catch (Exception $e) {
            return response()->json(array('message' => 'Internal server error.'), 500);
        }
    }

    /**
     * Delete task.
     *
     * @param  Integer  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($task)
    {
        try {
            Task::destroy($task);
            return response()->json(array('message' => 'Task deleted succesfully.'), 200);

        }
        catch (Exception $e) {
            return response()->json(array('message' => 'Internal server error.'), 500);
        }
    }
}
