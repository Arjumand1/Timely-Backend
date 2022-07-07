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
    route::post('signup', 'admincreate');
    route::post('login', 'login');
});

route::middleware('auth:sanctum')->group(function () {
    route::controller(AuthController::class)->group(function () {
        route::post('logout', 'logout');
        route::post('employee-signup', 'employeecreate');
    });
    route::controller(TimerController::class)->group(function () {
        route::post('timer/{id}', 'store');
        route::get('data/{id}', 'show');
    });
});


Route::controller(PasswordController::class)->group(function () {
    route::post('forget-password', 'forgot');
    route::post('reset-password', 'reset')->name('password.reset');
});
