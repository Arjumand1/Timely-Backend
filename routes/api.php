<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\TimerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    //admin signup route
    Route::post('signup', 'admincreate');
    //login
    Route::post('login', 'login');
});

//auth routes
Route::middleware('auth:sanctum')->group(function () {
    //AuthController
    Route::controller(AuthController::class)->group(function () {
        //logout
        Route::post('logout', 'logout');
        //employee signup form
        Route::post('employee-signup', 'employeecreate');
    });
    //timer controller
    Route::controller(TimerController::class)->group(function () {
        //store data
        Route::post('timer/{id}', 'store');
        //get data
        Route::get('data/{id}', 'show');
        //screenshots record
        route::get('image/{date}','view');
        //alldata
        route::get('details','alldata');

    });
});

//password forget and reset password routes
Route::controller(PasswordController::class)->group(function () {
    //send password reset email
    Route::post('forget-password', 'forgot');
    //reset password
    Route::post('reset-password', 'reset')->name('password.reset');
});


