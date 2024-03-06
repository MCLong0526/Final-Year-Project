<?php

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
/*
Route::middleware('auth:sanctum')->group(function () {

});*/

Route::controller(UserController::class)->prefix('users')->group(function () {
    Route::get('/', 'index');
    Route::post('/store', 'store');
    Route::delete('/delete/{user_id}', 'destroy');
    Route::put('/update/{user_id}', 'update');
});

Route::controller(RoleController::class)->prefix('roles')->group(function () {
    Route::get('/', 'index');

});
