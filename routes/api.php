<?php

use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AuditLogController;
use App\Http\Controllers\Api\Admin\ProjectController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\TaskController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Employee\EmpDashboardController;
use App\Http\Controllers\Api\Employee\EmpTaskController;
use App\Http\Controllers\Api\Employee\WorkLogController;
use App\Http\Controllers\Api\ProjectManager\PMDashboardController;
use App\Http\Controllers\Api\ProjectManager\PMProjectController;
use App\Http\Controllers\Api\ProjectManager\PMReportController;
use App\Http\Controllers\Api\ProjectManager\PMTaskController;
use App\Http\Controllers\Api\ProjectManager\WorkLogReplyController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// PUBLIC AUTH ROUTES
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login',          [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password',  [AuthController::class, 'resetPassword']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout',  [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me',       [AuthController::class, 'me']);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// ADMIN ROUTES
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('admin')
    ->middleware(['auth:api', 'role:admin'])
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [AdminDashboardController::class, 'index']);

        // Users
        Route::apiResource('users', UserController::class);

        // Projects
        Route::apiResource('projects', ProjectController::class);
        Route::post('projects/{project}/restore', [ProjectController::class, 'restore']);

        // Tasks
        Route::apiResource('tasks', TaskController::class);
        Route::post('tasks/{task}/assign', [TaskController::class, 'assign']);

        // Reports
        Route::get('reports/projects',               [ReportController::class, 'projectReport']);
        Route::get('reports/projects/{project}',     [ReportController::class, 'singleProjectReport']);
        Route::get('reports/employees',              [ReportController::class, 'employeeReport']);

        // Audit Logs
        Route::get('audit-logs',       [AuditLogController::class, 'index']);
        Route::get('audit-logs/{id}',  [AuditLogController::class, 'show']);
    });

// ─────────────────────────────────────────────────────────────────────────────
// PROJECT MANAGER ROUTES
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('pm')
    ->middleware(['auth:api', 'role:project-manager'])
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [PMDashboardController::class, 'index']);

        // Projects (scoped to PM's own)
        Route::get('projects',         [PMProjectController::class, 'index']);
        Route::get('projects/{project}', [PMProjectController::class, 'show'])->middleware('project.access');
        Route::put('projects/{project}', [PMProjectController::class, 'update'])->middleware('project.access');

        // Tasks (nested under project)
        Route::prefix('projects/{project}')->middleware('project.access')->group(function () {
            Route::get('tasks',                 [PMTaskController::class, 'index']);
            Route::post('tasks',                [PMTaskController::class, 'store']);
            Route::put('tasks/{task}',          [PMTaskController::class, 'update']);
            Route::delete('tasks/{task}',       [PMTaskController::class, 'destroy']);
            Route::post('tasks/{task}/assign',  [PMTaskController::class, 'assign']);
        });

        // Work Log Replies
        Route::get('work-logs/{workLog}',         [WorkLogReplyController::class, 'show']);
        Route::post('work-logs/{workLog}/reply',  [WorkLogReplyController::class, 'store']);

        // Reports (PM-scoped)
        Route::get('reports',               [PMReportController::class, 'index']);
        Route::get('reports/{project}',     [PMReportController::class, 'show'])->middleware('project.access');
    });

// ─────────────────────────────────────────────────────────────────────────────
// EMPLOYEE ROUTES
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('employee')
    ->middleware(['auth:api', 'role:employee'])
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [EmpDashboardController::class, 'index']);

        // Tasks (own only)
        Route::get('tasks',              [EmpTaskController::class, 'index']);
        Route::get('tasks/{task}',       [EmpTaskController::class, 'show'])->middleware('task.employee');
        Route::patch('tasks/{task}/status', [EmpTaskController::class, 'updateStatus'])->middleware('task.employee');

        // Work Logs
        Route::get('tasks/{task}/logs',    [WorkLogController::class, 'index'])->middleware('task.employee');
        Route::post('tasks/{task}/logs',   [WorkLogController::class, 'store'])->middleware('task.employee');
        Route::get('logs/{workLog}',       [WorkLogController::class, 'show']);
        Route::get('logs/{workLog}/download', [WorkLogController::class, 'downloadAttachment']);
    });
