<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\EmployeeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    //this method will register an admin
public function adminCreate(LoginRequest $request)
{
       
 $user=User::create($request->validated());
//generate token
$token = $user->createToken('my_Token')->plainTextToken;
            
 $response = [
'user' => $user,
'token' => $token,
                
 ];
 //response expected
return response()->json([
//'Messege'=>$message,
'data'=>$response,
'status'=>200,
            ]);
}

 //this method will register an employee
 public function employeeCreate(EmployeeRequest $request)
{
if (auth()->user()->role == 0) {
        
$employee=User::create($request->validated());
        
 Mail::to($employee)->send(new WelcomeMail($employee));

//expected response
return response()->json([
            
'employee'=>$employee,
'status'=>200,
]);
} else {
return response()->json(['message' => 'Unauthorized.'], 403);
}
}

//login
public function login(Request $request)
{
        
//validate request
$request->validate([
'email' => 'required|email',
'password' => 'required',
]);

$user = User::where('email', $request->email)->first();

if (!$user || $request->password !== $user->password) {
 return response([
'message' => 'The provided credentials are incorrect.'
], 403);
            
}
//generate token
 $token = $user->createToken('mytoken')->plainTextToken;

$response = [
'user' => $user,
'token' => $token
];
$message='user_login successfully' ;
//expected response
return response()->json([
'Messege'=>$message,
'data'=>$response,
'status'=>200,
]);
 }

    //user can logout through this method
 public function logout(Request $request)
{
$token = request()->user()->currentAccessToken()->token;
$request->user()->tokens()->where('token', $token)->delete();
 // expected response
 $message='Logged Out Succefully!!';
return response()->json([
'message' =>$message,
'status'=>200,
 ], 200);
        
}
}