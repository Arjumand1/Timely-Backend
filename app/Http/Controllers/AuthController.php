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
        try {
            $user=User::create($request->validated());
            return response()->json($user);
            //generate token
            $token = $data->createToken('my_Token')->plainTextToken;
            
            $response = [
                'user' => $user,
                'token' => $token,
                
            ];
            $message=trans('messege.admin_create');
            //response expected
            return response()->json([
                'Messege'=>$message,
                'data'=>$response,
                'status'=>200,
            ]);
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
    public function employeeCreate(EmployeeRequest $request)
    {
        if (auth()->user()->role == 0) {
            try {
                $employee=User::create($request->validated());
        
                Mail::to($employee)->send(new WelcomeMail($employee));

                //expected response
                $message=trans('messege.employee_Create');
                return response()->json([
                    'Messege'=>$message,
                    'employee'=>$employee,
                    'status'=>200,
                ]);
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
                ], 403);
            }
            //generate token
            $token = $user->createToken('mytoken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];
            $message=trans('messege.user_login');
            //expected response
            return response()->json([
                'Messege'=>$message,
                'data'=>$response,
                'status'=>200,
            ]);
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
            $message='Logged Out Succefully!!';
            return response()->json([
                'message' =>$message,
                'status'=>200,
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
