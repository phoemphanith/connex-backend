<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api as api;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Public Route
Route::post('/register', [api\AuthController::class, 'register']);
Route::post('/login', [api\AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['prefix' => 'user'], function () {
        Route::get('/profile', [api\AuthController::class, 'show']);
        Route::post('/update-profile', [api\AuthController::class, 'update']);
        Route::post('/change-password', [api\AuthController::class, 'changePassword']);
    });
});
