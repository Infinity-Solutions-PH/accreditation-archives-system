<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRoleStatus;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AccreditorAccountController;

Route::middleware(['web', 'auth:web', CheckRoleStatus::class])->group(function() {
    Route::post('/files/temp', [FileController::class, 'temp'])->name('api.files.temp');
    Route::post('/files/upload-chunk', [FileController::class, 'uploadChunk'])->name('api.files.upload_chunk');
    Route::post('/files/complete', [FileController::class, 'complete'])->name('api.files.complete');
    Route::post('/files/update-metadata', [FileController::class, 'updateMetadata'])->name('api.files.update_metadata');
    Route::post('/files/abort', [FileController::class, 'abort'])->name('api.files.abort');

    Route::post('/user-management/store', [UserController::class, 'store'])->name('api.user-management.store');
    Route::put('/user-management/{user}/role-status', [UserManagementController::class, 'updateRoleStatus'])->name('api.user-management.role-status');

    Route::post('/accreditors', [AccreditorAccountController::class, 'store'])->name('api.accreditors.store');
});
