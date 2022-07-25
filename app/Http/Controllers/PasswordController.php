<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

//controller for froget password and password reset
class PasswordController extends Controller
{
    //this method would send an email for resetting password
    public function forgot(request $request)
    {
        //validate the request
        $request->validate(['email' => 'required|email']);
        //sent password reset link
        $status = password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            $message = __($status);
            //expected response
            return response()->json([$message]);
        }
        //error
        $message = 'operation failed...';
        return response()->json([$message]);
    }

    //this method will allow you to create new password
    public function reset(request $request)
    {
        //validate the request
        $request->validate([
            'token' => "required",
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:08',             // must be at least 08 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
                'confirmed'
            ],
        ]);
        //reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forcefill([
                    'password' => Hash::make($password)
                ])->setRememberToken(str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            $message = __($status);
            //expected response
            return response()->json([$message]);
        }
        //error
        $message = 'Operation Failed...';
        return response()->json([$message]);
    }
}
