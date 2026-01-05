<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;

Route::get('/auth', [AuthController::class, 'index'])->name('auth');

Route::get('/dashboard', [LandingController::class, 'index'])->name('dashboard');
Route::get('/user-management', [LandingController::class, 'userManagement'])->name('user-management');
Route::get('/file-archives', [LandingController::class, 'fileArchives'])->name('file-archives');
Route::get('/activity-logs', [LandingController::class, 'activityLogs'])->name('activity-logs');
Route::get('/settings', [LandingController::class, 'userManagement'])->name('settings');

Route::get('/staff', [LandingController::class, 'staff'])->name('staff');
Route::get('/file-share', [LandingController::class, 'fileShare'])->name('file-share');