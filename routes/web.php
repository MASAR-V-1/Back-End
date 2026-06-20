<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\OrganizationApprovalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('dashboard/super-admin')->name('super-admin.')->group(function () {
    Route::get('/organizations', [OrganizationApprovalController::class, 'index'])->name('organizations.index');
    Route::post('/organizations/{organization}/approve', [OrganizationApprovalController::class, 'approve'])->name('organizations.approve');
    Route::post('/organizations/{organization}/reject', [OrganizationApprovalController::class, 'reject'])->name('organizations.reject');
});

require __DIR__ . '/auth.php';
