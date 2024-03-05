<?php

use App\Enums\UserRole;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AgoraTokenController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ScreenshotController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebRTCSignalController;
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

    Route::prefix('agora')->name('agora.')->group(function () {
        Route::controller(AgoraTokenController::class)->group(function () {
            Route::post('/token', 'generateToken')->name('token');
        });
    });

    Route::prefix('activity-logs')->name('activity-logs.')->controller(ActivityLogController::class)->group(function () {
        Route::post('/', 'store')->name('store');
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

    // MEETING ROUTES
    Route::prefix('meetings')->name('meetings.')->controller(MeetingController::class)->group(function () {
        Route::get('/hash/{hash}', 'showByHash')->name('showByHash');
        Route::post('/', 'store')->name('store');
        Route::put('/{meeting}', 'update')->name('update');
        Route::delete('/{meeting}', 'destroy')->name('destroy');
    });

    Route::prefix('activity-logs')->name('activity-logs.')->controller(ActivityLogController::class)->group(function () {
        Route::get('/staffs/{staffId}/meetings/{meetingId}', 'getStaffMeetingActivityLog')->name('staffs.meetings');
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

    // MEETING ROUTES
    Route::prefix('meetings')->name('meetings.')->controller(MeetingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{meeting}', 'show')->name('show');
        Route::get('/join/{hash}', 'join')->name('join');
        //        Route::post('/{meeting}/join', 'join')->name('join');
        Route::post('/{meeting}/leave', 'leave')->name('leave');
    });

    // WEBRTC ROUTES
    Route::prefix('meetings')->name('webrtc.')->controller(WebRTCSignalController::class)->group(function () {
        Route::post('/{meetingId}/offer', 'sendOffer')->name('offer');
        Route::post('/{meetingId}/answer', 'sendAnswer')->name('answer');
        Route::post('/{meetingId}/icecandidate', 'sendIceCandidate')->name('icecandidate');
        Route::post('/{meetingId}/signal', 'sendSignal')->name('signal');
    });

    // Screenshot routes
    Route::prefix('screenshots')->name('screenshots.')->controller(ScreenshotController::class)->group(function () {
        Route::get('/staffs', 'getStaffScreenshot')->name('staffs');
        Route::get('/meetings', 'getMeetingScreenshot')->name('meetings');
        Route::get('/staffs/{id}', 'getStaffScreenshotDetail')->name('staffs.show');
        Route::get('/meetings/{id}', 'getMeetingScreenshotDetail')->name('meetings.show');
    });

    // Activity Log routes
    Route::prefix('activity-logs')->name('activity-logs.')->controller(ActivityLogController::class)->group(function () {
        Route::get('/staffs/{id}', 'getStaffActivityLog')->name('staffs');
        Route::get('/{id}', 'show')->name('show');
    });

});
