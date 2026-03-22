<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\Accreditor\AuthController as AccreditorAuthController;

Route::get('/', function() {
    return redirect()->route('auth');
});
Route::middleware('guest')->group(function () {
    Route::get('/auth', [AuthController::class, 'index'])->name('auth');
    Route::get('/accreditor/auth', [AccreditorAuthController::class, 'index'])->name('accreditor.auth');

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::post('/logout', LogoutController::class) ->name('logout');

Route::middleware(['auth'])->group(function() {
    Route::get('/dashboard', [LandingController::class, 'index'])->name('dashboard');

    Route::get('/areas', [AreaController::class, 'index'])->name('areas');
    Route::get('/areas/{area:slug}', [AreaController::class, 'show'])->name('areas.slug');

    Route::get('/user-management', [UserManagementController::class, 'index'])->name('user-management');

    Route::get('/file-archives', [LandingController::class, 'fileArchives'])->name('file-archives');
    Route::get('/activity-logs', [LandingController::class, 'activityLogs'])->name('activity-logs');
    Route::get('/settings', [LandingController::class, 'userManagement'])->name('settings');

    Route::get('/staff', [LandingController::class, 'staff'])->name('staff');
    Route::get('/file-share', [LandingController::class, 'fileShare'])->name('file-share');

    Route::get('/videos/{file:id}', [VideoController::class, 'watch'])->name('videos.watch');
    Route::get('/videos/stream/{file:id}', [VideoStreamController::class, 'stream'])->name('videos.stream');
});

Route::prefix('api')->group(function () {    
    Route::post('/files/temp', [FileController::class, 'temp'])->name('files.temp');
    Route::post('/files/upload-chunk', [FileController::class, 'uploadChunk']);
    Route::post('/files/complete', [FileController::class, 'complete']);
    Route::post('/files/update-metadata', [FileController::class, 'updateMetadata']);
    Route::post('/files/abort', [FileController::class, 'abort']);

    Route::post('/user-management/store', [UserController::class, 'store'])->name('user-management.store');
});