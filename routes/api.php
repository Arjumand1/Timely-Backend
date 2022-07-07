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
    Route::post('signup', 'admincreate');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::post('employee-signup', 'employeecreate');
    });
    Route::controller(TimerController::class)->group(function () {
        Route::post('timer/{id}', 'store');
        Route::get('data/{id}', 'show');
    });
});


Route::controller(PasswordController::class)->group(function () {
    Route::post('forget-password', 'forgot');
    Route::post('reset-password', 'reset')->name('password.reset');
});
