<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::delete('/delete/{user_id}', 'destroy');
        Route::put('/update/{user_id}', 'update');
        Route::put('/update-profile/{user_id}', 'editProfile');
        Route::put('/update-password', 'editPassword');

    });

    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::get('/', 'index');

    });

    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::get('/get-user', 'getCurrentLoggedUser');
        Route::get('/get-user-by-token', 'getUserByToken');
        Route::post('/logout', 'logout');
    });

});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/authenticate', 'authenticate');

});
