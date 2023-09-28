<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use App\Http\Requests\loginRequest;
use App\Http\Requests\employeeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    //this method will register an admin
    public function admincreate(loginRequest $request)
    {
        try {
            $user=User::create($request->validated());
            return response()->json($user);
            //generate token
            $token = $data->createToken('my_Token')->plainTextToken;
            
            $response = [
                'data' => $user,
                'token' => $token,
            ];
            //response expected
            return response()->json($response, 200);
        } catch (Exception $e) {
            //thorw exception
            $message = $e->getMessage();
            var_dump('Exception Message: ' . $message);

            $code = $e->getCode();
            var_dump('Exception Code: ' . $code);

            $string = $e->__toString();
            var_dump('Exception String: ' . $string);

            exit;
        }
    }

    //this method will register an employee
    public function employeecreate(employeeRequest $request)
    {
        if (auth()->user()->role == 0) {
            try {
                $employee=User::create($request->validated());
        
                Mail::to($employee)->send(new WelcomeMail($employee));

                //expected response
                return response()->json($employee, 200);
            } catch (Exception $e) {
                //throw exception
                $message = $e->getMessage();
                var_dump('Exception Message: ' . $message);

                $code = $e->getCode();
                var_dump('Exception Code: ' . $code);

                $string = $e->__toString();
                var_dump('Exception String: ' . $string);

                exit;
            }
        } else {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
    }

    //login
    public function login(Request $request)
    {
        try {
            //validate request
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
            //generate token
            $token = $user->createToken('mytoken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            //expected response
            return response()->json($response, 200);
        } catch (Exception $e) {
            //throw execption
            $message = $e->getMessage();
            var_dump('Exception Message: ' . $message);

            $code = $e->getCode();
            var_dump('Exception Code: ' . $code);

            $string = $e->__toString();
            var_dump('Exception String: ' . $string);

            exit;
        }
    }

    //user can logout through this method
    public function logout(Request $request)
    {
        try {
            $token = request()->user()->currentAccessToken()->token;
            $request->user()->tokens()->where('token', $token)->delete();
            // expected response
            return response([
                'message' => 'Logged Out Succefully!!'
            ], 200);
        } catch (Exception $e) {
            //throw execption
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
