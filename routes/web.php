<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRoleStatus;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\AccreditationEventController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Accreditor\AuthController as AccreditorAuthController;

Route::get('/', function() {
    return redirect()->route('auth');
});
Route::middleware('guest')->group(function () {
    Route::get('/auth', [AuthController::class, 'index'])->name('auth');
    Route::post('/auth', [AuthController::class, 'store'])->name('auth.store');
    Route::get('/accreditor/auth', [AccreditorAuthController::class, 'index'])->name('accreditor.auth');

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('/accreditor/auth', [AccreditorAuthController::class, 'store'])->name('accreditor.login');

Route::post('/logout', LogoutController::class) ->name('logout');

Route::get('/onboarding/college', [OnboardingController::class, 'college'])->name('onboarding.college');
Route::post('/onboarding/college', [OnboardingController::class, 'storeCollege'])->name('onboarding.college.store');

Route::middleware('auth:web')->group(function() {
    Route::get('/onboarding/pending', [OnboardingController::class, 'pending'])->name('onboarding.pending');
    Route::get('/onboarding/rejected', [OnboardingController::class, 'rejected'])->name('onboarding.rejected');
    Route::get('/accreditor/no-active-event', [OnboardingController::class, 'noActiveEvent'])->name('accreditor.no-active-event');
});

Route::middleware(['auth:web,accreditor', CheckRoleStatus::class])->group(function() {
    Route::get('/dashboard', [LandingController::class, 'index'])->name('dashboard');
    Route::get('/taskforce', [LandingController::class, 'taskforce'])->name('taskforce');
    Route::get('/profile', [LandingController::class, 'profile'])->name('profile');
    Route::patch('/profile', [LandingController::class, 'updateProfile'])->name('profile.update');

    Route::get('/areas', [AreaController::class, 'index'])->name('areas');
    Route::get('/areas/{area:slug}', [AreaController::class, 'show'])->name('areas.slug');

    Route::get('/file-archives', [LandingController::class, 'fileArchives'])->name('file-archives');
    
    Route::get('/files/view/{file}', [FileController::class, 'view'])->name('files.view');
    Route::get('/files/download/{file}', [FileController::class, 'download'])->name('files.download');

    Route::get('/videos/{file:id}', [VideoController::class, 'watch'])->name('videos.watch');
    Route::get('/videos/stream/{file:id}', [VideoStreamController::class, 'stream'])->name('videos.stream');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // API Search
    Route::get('/api/search/global', [SearchController::class, 'globalSearch'])->name('api.search.global');
});

Route::middleware(['auth', CheckRoleStatus::class])->group(function() {
    Route::get('/user-management', [UserManagementController::class, 'index'])->name('user-management');

    Route::middleware(['role:admin'])->group(function() {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
    });

    Route::get('/accreditor/no-active-event', [OnboardingController::class, 'noActiveEvent'])->name('accreditor.no-active-event');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/staff', [LandingController::class, 'staff'])->name('staff');


    Route::get('/videos/{file:id}', [VideoController::class, 'watch'])->name('videos.watch');
    Route::get('/videos/stream/{file:id}', [VideoStreamController::class, 'stream'])->name('videos.stream');

    // Accreditation Events
    Route::get('/events', [AccreditationEventController::class, 'index'])->name('events.index');
    Route::post('/events', [AccreditationEventController::class, 'store'])->name('events.store');
    Route::put('/events/{accreditationEvent}', [AccreditationEventController::class, 'update'])->name('events.update');
});
