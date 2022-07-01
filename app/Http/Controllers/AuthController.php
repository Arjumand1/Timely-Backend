<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function admincreate(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => [
                'required',
                'string',
                'min:10',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'company_name' => 'required',
            'address' => 'required',
            'country' => 'required'
        ], [
            'password.min' => 'password must be greater than eight characters',
            'password.regex' => '1:must conatain one small alphabet '.' 2:must conatain one big alphabet'.' 3:must conatain a numeric digit' . ' 4:must contain one special character (! @ # $ %)',
        ]);

        // if(!$request->validated())

        $data = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
            'company_img' => $request->company_img,
            'address' => $request->address,
            'country' => $request->country,
            'role' => 0,
        ]);

        $token = $data->createToken('my_Token')->plainTextToken;
        $response = [
            'data' => $data,
            'token' => $token,
        ];

        $message = "succfully registered as admin";
        return response()->json([$response, $message]);
    }

    public function employeecreate(Request $request)
    {
       $validator = $request->validate([
              'name'=>'required',
              'name'=>'required',
              'name'=>'required',
       ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',

        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }
       if($user->role == 0)
       {
        $token = $user->createToken('mytoken');

        $response = [
            'user' => $user,
            'token' => $token
        ];

        $message = 'Successfully logged in as admin';
        return response()->json([$response, $message]);
       }
       if($user->role == 1)
       {
        $token = $user->createToken('mytoken');

        $response = [
            'user' => $user,
            'token' => $token
        ];

        $message = 'Successfully logged in as employee';
        return response()->json([$response, $message]);
       }

    }

    public function logout(request $request)
    {
        $request = auth()->user()->tokens()->delete();
        return response([
            'message' => 'Succefully Logged Out !!',
            $request
        ]);
    }
}
