<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\HolidayController;
use App\Http\Controllers\Api\LeaveBalanceController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\PositionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::middleware('can:employees.view')->apiResource('employees', EmployeeController::class)->only(['index', 'show']);
    Route::middleware('can:employees.manage')->apiResource('employees', EmployeeController::class)->only(['store', 'update', 'destroy']);

    Route::middleware('can:departments.view')->apiResource('departments', DepartmentController::class)->only(['index', 'show']);
    Route::middleware('can:departments.manage')->apiResource('departments', DepartmentController::class)->only(['store', 'update', 'destroy']);

    Route::middleware('can:positions.view')->apiResource('positions', PositionController::class)->only(['index', 'show']);
    Route::middleware('can:positions.manage')->apiResource('positions', PositionController::class)->only(['store', 'update', 'destroy']);

    Route::middleware('can:documents.view')->apiResource('documents', DocumentController::class)->only(['index', 'show']);
    Route::middleware('can:documents.manage')->apiResource('documents', DocumentController::class)->only(['store', 'update', 'destroy']);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])
        ->middleware(['can:documents.view', 'throttle:30,1'])
        ->name('documents.download')
        ->whereNumber('document');

    Route::middleware('can:holidays.view')->apiResource('holidays', HolidayController::class)->only(['index', 'show']);
    Route::middleware('can:holidays.manage')->apiResource('holidays', HolidayController::class)->only(['store', 'update', 'destroy']);

    Route::middleware('can:attendance.view')->apiResource('attendances', AttendanceController::class)->only(['index', 'show']);
    Route::middleware('can:attendance.manage')->apiResource('attendances', AttendanceController::class)->only(['store', 'update']);

    Route::middleware('can:leaves.view')->apiResource('leaves', LeaveController::class)
        ->only(['index', 'show'])->parameters(['leaves' => 'leave']);
    Route::middleware('can:leaves.manage')->apiResource('leaves', LeaveController::class)
        ->only(['store', 'update', 'destroy'])->parameters(['leaves' => 'leave']);
    Route::post('leaves/{leave}/decision', [LeaveController::class, 'decide'])
        ->middleware('can:leaves.approve')
        ->whereNumber('leave');

    Route::middleware('can:leave-balances.view')->apiResource('leave-balances', LeaveBalanceController::class)->only(['index', 'show']);
    Route::middleware('can:leave-balances.manage')->apiResource('leave-balances', LeaveBalanceController::class)->only(['store', 'update', 'destroy']);
});
