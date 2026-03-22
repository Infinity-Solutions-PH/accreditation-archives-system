<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AccreditorAccountController;

Route::post('/files/temp', [FileController::class, 'temp'])->name('files.temp');
Route::post('/files/upload-chunk', [FileController::class, 'uploadChunk']);
Route::post('/files/complete', [FileController::class, 'complete']);
Route::post('/files/update-metadata', [FileController::class, 'updateMetadata']);
Route::post('/files/abort', [FileController::class, 'abort']);

Route::post('/user-management/store', [UserController::class, 'store'])->name('user-management.store');
Route::put('/user-management/{user}/role-status', [UserManagementController::class, 'updateRoleStatus'])->name('user-management.role-status');

Route::post('/accreditors', [AccreditorAccountController::class, 'store'])->name('accreditors.store');
