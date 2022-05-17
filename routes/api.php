<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/is-email-exists', [App\Http\Controllers\Api\AuthController::class, 'isEmailExists']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register-user', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/forgot-password', [App\Http\Controllers\Api\AuthController::class,'forgot_password']);
Route::post('/profileImage', [App\Http\Controllers\Api\AuthController::class, 'profileImage']);


Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('/updateProfile', [App\Http\Controllers\Api\AuthController::class, 'updateProfile']);
    Route::post('/updatePassword', [App\Http\Controllers\Api\AuthController::class, 'updatePassword']);
    Route::get('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/getuserprofile', [App\Http\Controllers\Api\AuthController::class, 'userProfile']);


    // change Password
    Route::post('/changePassword', [App\Http\Controllers\Api\AuthController::class, 'changePassword']);

});
