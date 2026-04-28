<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Middleware\CheckRoleStatus;
use App\Http\Controllers\FileShareController;
use App\Http\Controllers\EventAccreditorController;
use App\Http\Controllers\AccreditationEventController;

Route::middleware(['auth:web,accreditor', CheckRoleStatus::class])->group(function() {
    Route::post('/files/temp', [FileController::class, 'temp'])->name('api.files.temp');
    Route::post('/files/upload-chunk', [FileController::class, 'uploadChunk'])->name('api.files.upload_chunk');
    Route::post('/files/complete', [FileController::class, 'complete'])->name('api.files.complete');
    Route::post('/files/update-metadata', [FileController::class, 'updateMetadata'])->name('api.files.update_metadata');
    Route::post('/files/abort', [FileController::class, 'abort'])->name('api.files.abort');

    Route::get('/events/{event}/accreditors', [EventAccreditorController::class, 'index'])->name('events.accreditors.index');
    Route::post('/events/{event}/accreditors', [EventAccreditorController::class, 'store'])->name('events.accreditors.store');
    Route::delete('/accreditors/{accreditor}', [EventAccreditorController::class, 'destroy'])->name('events.accreditors.destroy');

    Route::post('/events/share', [AccreditationEventController::class, 'shareToFile'])->name('events.share');
    Route::get('/users/search-shareable', [FileShareController::class, 'searchShareableUsers'])->name('api.users.search_shareable');
    Route::post('/files/share-to-user', [FileShareController::class, 'shareToUser'])->name('api.files.share_to_user');
});
