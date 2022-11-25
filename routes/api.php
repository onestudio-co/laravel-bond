<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationTokenController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SocialLoginController;
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
Route::middleware('language')->group(function () {
    Route::prefix('users')->group(function () {
        Route::middleware(['throttle:1,2', 'guest'])->post('anonymous-login', [LoginController::class, 'anonymous']);
        Route::post('login', [LoginController::class, 'login'])->middleware('throttle:1,2');
        Route::post('register', [RegisterController::class, 'store'])->middleware('throttle:1,2');
        Route::post('social-login', SocialLoginController::class)->middleware('throttle:1,2');
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/notifications/update-token', NotificationTokenController::class);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll']);
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'read']);
        Route::post('/posts/random', [PostController::class, 'random']);
    });
});
