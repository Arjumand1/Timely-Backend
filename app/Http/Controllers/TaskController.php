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
                'user' => $user,
                'token' => $token,
            ];
            $message=trans('messege.store_task');
            //response expected
            return response()->json([
               'Messege'=>$message,
               'data'=>$response,
               'status'=>200,
            ]);

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
            $message=trans('messege.get_tasks');
            return response()->json([
            'Messege'=>$message,
            'data'=>$task,
            'status'=>200,
            ]);
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
