<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\RegistrationController;
use App\Http\Controllers\Api\V1\VerificationController;
use App\Http\Controllers\Api\V1\ScanController;
use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\OrganizationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API endpoints
Route::prefix('v1')->group(function () {
    // Public registration
    Route::post('/events/{event_uuid}/register', [RegistrationController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('api.v1.register');

    // Email/OTP verification
    Route::post('/verify/{token}', [VerificationController::class, 'verify'])
        ->name('api.v1.verify');
});

// Authenticated API endpoints
Route::prefix('v1')->name('api.v1.')->middleware(['auth:sanctum'])->group(function () {
    // Current user
    Route::get('/user', function (Request $request) {
        return $request->user()->load('organization');
    });

    // Organizations
    Route::apiResource('organizations', OrganizationController::class);

    // Events
    Route::apiResource('events', EventController::class);
    Route::post('/events/{uuid}/publish', [EventController::class, 'publish'])->name('events.publish');
    Route::post('/events/{uuid}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
    Route::post('/events/{uuid}/duplicate', [EventController::class, 'duplicate'])->name('events.duplicate');

    // QR Scan / Check-in
    Route::post('/scan', [ScanController::class, 'process'])->name('scan');
    Route::post('/manual-checkin', [ScanController::class, 'manualCheckIn'])->name('manual-checkin');

    // Attendance
    Route::get('/events/{uuid}/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/events/{uuid}/attendance/stats', [AttendanceController::class, 'stats'])->name('attendance.stats');

    // Reports
    Route::get('/events/{uuid}/reports/{type}', [ReportController::class, 'show'])->name('reports');
    Route::get('/events/{uuid}/reports/{type}/export', [ReportController::class, 'export'])->name('reports.export');
});
