<?php

use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\ConsultationServiceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DirectorDashboardController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ImpactMetricController;
use App\Http\Controllers\Admin\MonevController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->isDirector() && ! $user->isAdmin()) {
        return redirect()->route('director.dashboard');
    }

    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

// Director Portal (Role-based URL: /director/*)
Route::middleware(['auth'])->prefix('director')->name('director.')->group(function () {
    Route::get('/dashboard', [DirectorDashboardController::class, 'index'])->name('dashboard');

    // Director Approval Workflow
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/approvals/{approval}', [ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('/approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    // Director Programs View & Action
    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');
    Route::post('/programs/{program}/approve', [ProgramController::class, 'approve'])->name('programs.approve');
    Route::post('/programs/{program}/reject', [ProgramController::class, 'reject'])->name('programs.reject');

    // Director Monitoring & Evaluasi
    Route::get('/monev', [MonevController::class, 'index'])->name('monev.index');
    Route::get('/monev/{monev}', [MonevController::class, 'show'])->name('monev.show');

    // Director Kinerja & Dampak (IKU)
    Route::get('/impact', [ImpactMetricController::class, 'index'])->name('impact.index');
    Route::get('/impact/{impact}', [ImpactMetricController::class, 'show'])->name('impact.show');

    // Director Documents
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Program Management & Workflow
    Route::post('/programs/{program}/submit', [ProgramController::class, 'submit'])->name('programs.submit');
    Route::post('/programs/{program}/approve', [ProgramController::class, 'approve'])->name('programs.approve');
    Route::post('/programs/{program}/reject', [ProgramController::class, 'reject'])->name('programs.reject');
    Route::post('/programs/{program}/start', [ProgramController::class, 'start'])->name('programs.start');
    Route::post('/programs/{program}/complete', [ProgramController::class, 'complete'])->name('programs.complete');
    Route::resource('programs', ProgramController::class);

    // Approval Workflow
    Route::get('/approvals', ApprovalController::class.'@index')->name('approvals.index');
    Route::get('/approvals/{approval}', ApprovalController::class.'@show')->name('approvals.show');
    Route::post('/approvals/{approval}/approve', ApprovalController::class.'@approve')->name('approvals.approve');
    Route::post('/approvals/{approval}/reject', ApprovalController::class.'@reject')->name('approvals.reject');

    // Layanan & Konsultasi (Services & Consultation)
    Route::post('/services/{service}/schedule', [ConsultationServiceController::class, 'schedule'])->name('services.schedule');
    Route::post('/services/{service}/complete', [ConsultationServiceController::class, 'complete'])->name('services.complete');
    Route::post('/services/{service}/reject', [ConsultationServiceController::class, 'reject'])->name('services.reject');
    Route::resource('services', ConsultationServiceController::class);

    // Monitoring & Evaluasi (Monev)
    Route::resource('monev', MonevController::class);

    // Kinerja & Dampak (Performance & Impact)
    Route::resource('impact', ImpactMetricController::class);

    Route::get('/tasks', fn () => redirect()->route('admin.dashboard'))->name('tasks.index');
    Route::get('/tasks/create', fn () => redirect()->route('admin.dashboard'))->name('tasks.create');

    // Document Management
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents', fn () => redirect()->route('admin.dashboard'))->name('documents.index');

    // User Management
    Route::patch('/users/{user}/toggle-status', UserController::class.'@toggleStatus')->name('users.toggle-status');
    Route::resource('users', UserController::class);

    // Role & Permission Management
    Route::resource('roles', RoleController::class);

    Route::get('/audit-logs', fn () => redirect()->route('admin.dashboard'))->name('audit-logs.index');
    Route::get('/settings', fn () => redirect()->route('admin.dashboard'))->name('settings.index');
    Route::get('/notifications', fn () => redirect()->route('admin.dashboard'))->name('notifications.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
