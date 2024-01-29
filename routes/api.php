<?php

use App\Enums\UserRole;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UploadController;
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

Route::middleware('api')->group(function () {
    // AUTHENTICATION ROUTES
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('/google', 'loginWithGoogle')->name('google');
            Route::post('/refresh', 'refresh')->name('refresh');
        });
    });
});

Route::middleware(['api', 'role:'.UserRole::ADMIN])->group(function () {
    // USER ROUTES
    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::post('/', 'store')->name('store');
        Route::put('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

    // UPLOAD ROUTES
    Route::prefix('upload')->name('upload.')->controller(UploadController::class)->group(function () {
        Route::post('/', 'upload')->name('upload');
    });
});

Route::middleware(['api', 'auth'])->group(function () {
    // AUTHENTICATION ROUTES
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('/me', 'me')->name('me');
        });
    });

    // USER ROUTES
    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/me', 'me')->name('me');
        Route::get('/{user}', 'show')->name('show');
    });
});
