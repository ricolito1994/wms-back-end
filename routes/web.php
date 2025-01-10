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
// route body
Route::group(['middleware' => 'jwt'] , function () {
    Route::group(['prefix' => 'unit'], function () {
        Route::post('update/{id}', [UnitController::class, 'update']);
        Route::post('create', [UnitController::class, 'create']);
        Route::delete('delete', [UnitController::class, 'delete']);  
        Route::get('show/{id?}', [UnitController::class, 'show']);
    });
    Route::group(['prefix' => 'employee', 'namespace' => 'App\Http\Controllers'], function () {
        Route::post('create',  'HumanResourceController@create');
        Route::post('{userId}/update',  'HumanResourceController@update');
        Route::get('show',  'HumanResourceController@index');
        Route::get('{userId}',  'HumanResourceController@show');
        Route::delete('{userId}',  'HumanResourceController@delete');

        Route::group(['prefix' => 'crew'], function () {
            Route::post('{unitId}',  'HumanResourceController@createCrew');
            Route::get('{unitId}',  'HumanResourceController@getCrew');
        });
    });
    Route::group(['prefix' => 'landmark', 'namespace' => 'App\Http\Controllers'], function () {
        Route::get('{type}',  'LandmarksController@show');
        Route::get('{landmarkId}/{type}',  'LandmarksController@get');
        Route::post('{type}',  'HumanResourceController@create');
        Route::patch('{landmarkId}/{type}',  'HumanResourceController@update');
        Route::delete('{landmarkId}/{type}',  'HumanResourceController@delete');
    });
});
