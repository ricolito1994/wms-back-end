<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WasteManagementController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login' , [AuthController::class, 'login']);
    Route::group(['middleware' => 'jwt'] , function () {
        Route::post('logout' , [AuthController::class, 'logout']);
        Route::get('refresh' , [AuthController::class, 'refresh']);
        Route::get('me' , [AuthController::class, 'me']);
    });
});

Route::group(['middleware' => 'jwt'] , function () {

});
