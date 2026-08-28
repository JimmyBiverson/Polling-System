<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VoteSubmissionController;
use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ── Dashboard ─────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('dashboard'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth.agent');

// ── Vote Submissions ──────────────────────────────────────
Route::get('/submit', [VoteSubmissionController::class, 'create'])->name('votes.create')->middleware('auth.agent');
Route::post('/submit', [VoteSubmissionController::class, 'store'])->name('votes.store')->middleware('auth.agent');
Route::get('/submission/{submission}', [VoteSubmissionController::class, 'show'])->name('votes.show')->middleware('auth.agent');
Route::post('/submission/{submission}/verify', [VoteSubmissionController::class, 'verify'])
    ->middleware(['auth.agent', 'admin'])
    ->name('votes.verify');
Route::post('/bulk-submit', [VoteSubmissionController::class, 'bulkStore'])->name('votes.bulk')->middleware('auth.agent');

// ── Reports ───────────────────────────────────────────────
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('auth.agent');
Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate')->middleware('auth.agent');
Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])
    ->middleware(['auth.agent', 'admin'])
    ->name('reports.export');

// ── Admin: Manage ─────────────────────────────────────────
Route::prefix('manage')->name('manage.')->middleware(['auth.agent', 'admin'])->group(function () {
    Route::get('/', [ManageController::class, 'index'])->name('index');

    Route::post('/counties', [ManageController::class, 'storeCounty'])->name('counties.store');
    Route::delete('/counties/{county}', [ManageController::class, 'destroyCounty'])->name('counties.destroy');

    Route::post('/constituencies', [ManageController::class, 'storeConstituency'])->name('constituencies.store');
    Route::delete('/constituencies/{constituency}', [ManageController::class, 'destroyConstituency'])->name('constituencies.destroy');

    Route::post('/wards', [ManageController::class, 'storeWard'])->name('wards.store');
    Route::delete('/wards/{ward}', [ManageController::class, 'destroyWard'])->name('wards.destroy');

    Route::post('/stations', [ManageController::class, 'storeStation'])->name('stations.store');
    Route::delete('/stations/{station}', [ManageController::class, 'destroyStation'])->name('stations.destroy');

    Route::post('/candidates', [ManageController::class, 'storeCandidate'])->name('candidates.store');
    Route::delete('/candidates/{candidate}', [ManageController::class, 'destroyCandidate'])->name('candidates.destroy');

    Route::post('/election-types', [ManageController::class, 'storeElectionType'])->name('electionTypes.store');
    Route::delete('/election-types/{electionType}', [ManageController::class, 'destroyElectionType'])->name('electionTypes.destroy');

    // ── Super Admin User & Role Management ─────────────────
    Route::get('/users', [ManageController::class, 'users'])->name('users.index');
    Route::post('/users', [ManageController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [ManageController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/toggle-status', [ManageController::class, 'toggleUserStatus'])->name('users.toggleStatus');
    Route::post('/users/{user}/reset-password', [ManageController::class, 'resetUserPassword'])->name('users.resetPassword');
    Route::delete('/users/{user}', [ManageController::class, 'destroyUser'])->name('users.destroy');

    // ── Super Admin Security Audit Logs ─────────────────────
    Route::get('/audit-logs', [ManageController::class, 'auditLogs'])->name('auditLogs');

    // ── Super Admin System Data & Reset Controls ───────────
    Route::post('/re-tally', [ManageController::class, 'reTally'])->name('reTally');
    Route::post('/clear-test-data', [ManageController::class, 'clearTestData'])->name('clearTestData');
});

// ── Super Admin Submission Overrides ───────────────────────
Route::post('/submission/{submission}/override', [VoteSubmissionController::class, 'overrideStatus'])
    ->middleware(['auth.agent', 'admin'])
    ->name('votes.override');
