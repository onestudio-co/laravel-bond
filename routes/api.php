<?php

use App\Http\Controllers\LoginController;
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

Route::prefix('users')->group(function () {
    Route::middleware(['throttle:1,2', 'guest'])->post('anonymous-login', [LoginController::class, 'anonymous']);
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:1,2');
});