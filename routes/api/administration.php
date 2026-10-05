<?php

use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SalaryController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::middleware('can:payroll.view')->apiResource('salaries', SalaryController::class)->only(['index', 'show']);
    Route::middleware('can:payroll.manage')->apiResource('salaries', SalaryController::class)->only(['store', 'update', 'destroy']);
    Route::post('salaries/{salary}/decision', [SalaryController::class, 'decide'])
        ->middleware('can:payroll.approve')
        ->whereNumber('salary');
    Route::post('salaries/{salary}/pay', [SalaryController::class, 'pay'])
        ->middleware(['can:payroll.pay', 'throttle:10,1'])
        ->whereNumber('salary');

    Route::middleware('can:roles.manage')->apiResource('roles', RoleController::class);
    Route::middleware('can:permissions.manage')->apiResource('permissions', PermissionController::class);

    Route::middleware('can:users.manage')->apiResource('users', UserController::class)
        ->only(['index', 'store', 'update']);
});
