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
//response expected
return response()->json([

'data'=>$response,
'status'=>200,
]);
       
}

 public function show()
{

$tasks = Task::where('user_id', auth()->user()->id)->get();

 return response()->json([
'data'=>$task,
'status'=>200,
 ]);
    }
}
