<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Exception;

class AuthController extends Controller
{
    //this method will register an admin
    public function admincreate(Request $request)
    {
        try {
            //validate the request
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'password' => [
                    'required',
                    'string',
                    'min:8',             // must be at least 08 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                'company_name' => 'required',
                'address' => 'required',
                'country' => 'required'
            ], [
                'password.min' => 'password must not be greater than eight characters',
                'password.regex' => '1:must conatain one small alphabet ' . ' 2:must conatain one big alphabet' . ' 3:must conatain a numeric digit' . ' 4:must contain one special character (! @ # $ %)',
            ]);


            //create employee
            $data = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_name' => $request->company_name,
                'company_img' => $request->company_img,
                'address' => $request->address,
                'country' => $request->country,
                'role' => 0,   //0 role defined for admins only
            ]);

            //generate token
            $token = $data->createToken('my_Token')->plainTextToken;
            $response = [
                'data' => $data,
                'token' => $token,
            ];

            //      $message = "successfully registered as admin";
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
        //it should provide all the data with token and message
        return response()->json([$response], 200);
    }

    //this method will register an employee
    public function employeecreate(Request $request)
    {
        if (auth()->user()->role == 0) {
        try {
            //validate the request
            $request->validate(
                [
                    'name' => 'required',
                    'department' => 'required',
                    'email' => 'required',
                    'password' => [
                        'required',
                        'string',
                        'min:8',             // must be at least 08 characters in length
                        'regex:/[a-z]/',      // must contain at least one lowercase letter
                        'regex:/[A-Z]/',      // must contain at least one uppercase letter
                        'regex:/[0-9]/',      // must contain at least one digit
                        'regex:/[@$!%*#?&]/', // must contain a special character
                    ],
                    'designation' => 'required'
                ],
                [
                    'password.min' => 'password must not be greater than eight characters',
                    'password.regex' => '1:must conatain one small alphabet ' . ' 2:must conatain one big alphabet' . ' 3:must conatain a numeric digit' . ' 4:must contain one special character (! @ # $ %)',
                ]
            );
            //create employee

            $employee = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'department' => $request->department,
                'image' => $request->image,
                'designation' => $request->designation,
                'role' => 1,
            ]);
            //send mail with credentials
        //    Mail::to($employee)->send(new WelcomeMail($employee));

            //    $message = 'Employee Registered And Mail sent Successfully';
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
        //it should provide all the data with token and message
        return response()->json([$employee], 200);
    } else {
        $message = 'Unauthorized';
        return response()->json($message, 403);
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
            //Auth failed
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

            // $message = 'Successfully logged in ';
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
        //it should provide data with token and message
        return response()->json([$response], 200);
    }

    //user can logout through this method
    public function logout(Request $request)
    {
        try {
            $tokenId = request()->user()->currentAccessToken()->token;
            $request->user()->tokens()->where('token', $tokenId)->delete();
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
        // expected response
        return response([
            'message' => 'Succefully Logged Out !!'

        ], 200);
    }
}
