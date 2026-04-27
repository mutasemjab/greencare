<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lab\Auth\LoginController;
use App\Http\Controllers\Lab\DashboardController;
use App\Http\Controllers\Lab\AppointmentController;
use App\Http\Controllers\Lab\TypeMedicalTestController;
use App\Http\Controllers\Lab\LabNotificationController;

Route::prefix('lab')->name('lab.')->group(function () {
    
    // Guest Routes (Login)
    Route::middleware('guest:lab')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    });
    
    // Protected Routes (Authenticated Labs)
    Route::middleware('auth:lab')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Medical Test Types (CRUD)
        Route::resource('type-medical-tests', TypeMedicalTestController::class)
            ->except(['show'])
            ->names('type-medical-tests');

        // Notification Routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/',                  [LabNotificationController::class, 'index'])->name('index');
            Route::post('/{id}/mark-read',   [LabNotificationController::class, 'markAsRead'])->name('markAsRead');
            Route::post('/mark-all-read',    [LabNotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
            Route::get('/unread-count',      [LabNotificationController::class, 'getUnreadCount'])->name('unreadCount');
            Route::get('/latest',            [LabNotificationController::class, 'getLatest'])->name('latest');
            Route::delete('/{id}',           [LabNotificationController::class, 'destroy'])->name('destroy');
            Route::delete('/read/all',       [LabNotificationController::class, 'deleteAllRead'])->name('deleteAllRead');
        });

        // Appointments Routes
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/', [AppointmentController::class, 'index'])->name('index');
            Route::get('/{type}/{id}', [AppointmentController::class, 'show'])->name('show');
            Route::post('/{type}/{id}/update-status', [AppointmentController::class, 'updateStatus'])->name('update-status');
            Route::post('/{type}/{id}/upload-results', [AppointmentController::class, 'uploadResults'])->name('upload-results');
            Route::delete('/results/{resultId}/delete-file', [AppointmentController::class, 'deleteFile'])->name('delete-file');
        });
    });
});