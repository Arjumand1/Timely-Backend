<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Exception;

class TaskController extends Controller
{
    public function store(TaskRequest $request)
    {
            $task=Task::create($request->validated());
            return response()->json($task, 200);
            $token = $data->createToken('my_Token')->plainTextToken;
            $response = [
                'user' => $user,
                'token' => $token,
            ];
            $message='stored_task';
            //response expected
            return response()->json([
               'Messege'=>$message,
               'data'=>$response,
               'status'=>200,
            ]);
       
    }


    public function show()
    {

            $tasks = Task::where('user_id', auth()->user()->id)->get();
            $message='get_tasks';
            return response()->json([
            'Messege'=>$message,
            'data'=>$task,
            'status'=>200,
            ]);
    }
}
