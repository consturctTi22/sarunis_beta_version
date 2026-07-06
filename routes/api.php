<?php

use App\Http\Controllers\OfflineAttendanceController;
use Illuminate\Support\Facades\Route;

/**
 * API Routes - Offline Attendance
 * 
 * Base URL: /api/attendance/offline
 */

Route::prefix('attendance/offline')->group(function () {
    // Record attendance offline (no auth required for offline devices)
    Route::post('/record', [OfflineAttendanceController::class, 'recordAttendance'])
        ->name('offline.record');

    // Get unsynced records
    Route::get('/unsynced', [OfflineAttendanceController::class, 'getUnsyncedRecords'])
        ->name('offline.unsynced');

    Route::get('/unsynced/device', [OfflineAttendanceController::class, 'getUnsyncedByDevice'])
        ->name('offline.unsynced.device');

    // Sync records (auto-sync on network connection)
    Route::post('/sync', [OfflineAttendanceController::class, 'syncRecords'])
        ->name('offline.sync');

    Route::post('/sync/{id}', [OfflineAttendanceController::class, 'syncRecord'])
        ->name('offline.sync.single');

    // Retry failed syncs
    Route::post('/sync/retry', [OfflineAttendanceController::class, 'retrySyncErrors'])
        ->name('offline.sync.retry');

    // Statistics
    Route::get('/statistics', [OfflineAttendanceController::class, 'getStatistics'])
        ->name('offline.stats');

    Route::get('/statistics/device', [OfflineAttendanceController::class, 'getDeviceStatistics'])
        ->name('offline.stats.device');

    // Get attendance data
    Route::get('/student/{studentId}/{date}', [OfflineAttendanceController::class, 'getStudentAttendance'])
        ->name('offline.student.attendance');

    Route::get('/device/range', [OfflineAttendanceController::class, 'getDeviceAttendanceByDateRange'])
        ->name('offline.device.range');

    // Maintenance (delete old records)
    Route::delete('/clear-old', [OfflineAttendanceController::class, 'clearOldRecords'])
        ->name('offline.clear.old');
});
