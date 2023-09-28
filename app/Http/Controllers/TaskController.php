<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Exception;

class TaskController extends Controller
{
    public function store(TaskRequest $request)
    {
        try {
            $task=Task::create($request->validated());
            return response()->json($task, 200);
            $token = $data->createToken('my_Token')->plainTextToken;
            $response = [
                'data' => $user,
                'token' => $token,
            ];
            //response expected
            return response()->json($response, 200);
          /*  $request->validate([
                'task_desc' => 'required',
                'task_title' => 'required'
            ]);
            
            $data = new Task;            
            $data->user_id = auth()->user()->id;
            $data->task_desc = $request->task_desc;
            $data->task_title = $request->task_title;
            $data->save();

            return response()->json($data, 200);*/
        } catch (Exception $e) {
            $message = $e->getMessage();
            var_dump('Exception Message: ' . $message);

            $code = $e->getCode();
            var_dump('Exception Code: ' . $code);

            $string = $e->__toString();
            var_dump('Exception String: ' . $string);

            exit;
        }
    }


    public function show()
    {
        try {
            $tasks = Task::where('user_id', auth()->user()->id)->get();
            return response()->json($tasks, 200);
        } catch (Exception $e) {
            $message = $e->getMessage();
            var_dump('Exception Message: ' . $message);

            $code = $e->getCode();
            var_dump('Exception Code: ' . $code);

            $string = $e->__toString();
            var_dump('Exception String: ' . $string);

            exit;
        }
    }
}
