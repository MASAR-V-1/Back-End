<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\OrganizationApprovalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('dashboard/super-admin')->name('super-admin.')->group(function () {
        Route::get('/organizations', [OrganizationApprovalController::class, 'index'])->name('organizations.index');
        Route::get('organizations/{organization}', [OrganizationApprovalController::class, 'show'])->withTrashed()->name('organizations.show');
        Route::patch('organizations/{organization}/toggle-active', [OrganizationApprovalController::class, 'toggleActive'])->name('organizations.toggleActive');
        Route::post('/organizations/{organization}/approve', [OrganizationApprovalController::class, 'approve'])->name('organizations.approve');
        Route::post('/organizations/{organization}/request-changes', [OrganizationApprovalController::class, 'requestChanges'])->name('organizations.requestChanges');
        Route::post('/organizations/{organization}/reject', [OrganizationApprovalController::class, 'reject'])->name('organizations.reject');
    });
});


require __DIR__ . '/auth.php';
