<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Accreditor\AuthController as AccreditorAuthController;

Route::get('/', function() {
    return redirect('/auth');
});
Route::get('/auth', [AuthController::class, 'index'])->name('auth');
Route::get('/accreditor/auth', [AccreditorAuthController::class, 'index'])->name('accreditor.auth');

Route::post('/logout', LogoutController::class) ->name('logout');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

Route::get('/user-management', [LandingController::class, 'userManagement'])->name('user-management');
Route::get('/file-archives', [LandingController::class, 'fileArchives'])->name('file-archives');
Route::get('/activity-logs', [LandingController::class, 'activityLogs'])->name('activity-logs');
Route::get('/settings', [LandingController::class, 'userManagement'])->name('settings');

Route::get('/staff', [LandingController::class, 'staff'])->name('staff');
Route::get('/file-share', [LandingController::class, 'fileShare'])->name('file-share');

Route::middleware(['auth'])->group(function() {
    Route::get('/dashboard', [LandingController::class, 'index'])->name('dashboard');
    Route::get('/areas', [AreaController::class, 'index'])->name('areas');
    Route::get('/areas/{area:slug}', [AreaController::class, 'show'])->name('areas.slug');
});

Route::prefix('api')->group(function () {
    Route::post('/documents/temp', [DocumentController::class, 'temp'])->name('documents.temp');
    Route::post('/documents/upload-chunk', [DocumentController::class, 'uploadChunk']);
    Route::post('/documents/complete', [DocumentController::class, 'complete']);
    Route::post('/documents/update-metadata', [DocumentController::class, 'updateMetadata']);
    Route::post('/documents/abort', [DocumentController::class, 'abortUpload']);
});