<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// ─── GUEST ROUTES ──────────────────────────────────────────────
Route::middleware('guest:web')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    // Map Breeze routes to our custom auth controller (simplified)
    Route::get('forgot-password', function() { return view('auth.forgot-password'); })->name('password.request');
});

// ─── AUTHENTICATED ROUTES ──────────────────────────────────────
Route::middleware('auth:web')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Web UI Routes
    Route::resource('projects', \App\Http\Controllers\Web\ProjectWebController::class)->names('web.projects');
    Route::resource('tasks', \App\Http\Controllers\Web\TaskWebController::class)->names('web.tasks');

    Route::get('/reports', [\App\Http\Controllers\Web\ReportWebController::class, 'index'])->name('web.reports.index');
    Route::get('/users', [\App\Http\Controllers\Web\UserWebController::class, 'index'])->name('web.users.index');
    Route::get('/audit-logs', [\App\Http\Controllers\Web\AuditLogWebController::class, 'index'])->name('web.audit-logs.index');
});
