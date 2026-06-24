<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\EmployeeActivationController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\OrganizationRegistrationController;
use App\Http\Controllers\Api\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes (بدون تسجيل دخول)
Route::post('/register-organization', [OrganizationRegistrationController::class, 'store']);
Route::post('/email/resend-verification', [EmailVerificationController::class, 'resend'])
    ->middleware('throttle:3,1');
Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('api.verification.verify');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// Protected routes (لازم Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // employees بس org_admin
    Route::middleware('role:org_admin')->group(function () {
        Route::post('/employees', [EmployeeController::class, 'store']);
        Route::get('/employees', [EmployeeController::class, 'index']);
        Route::post('/employees/{employee}/resend-activation', [EmployeeActivationController::class, 'resend']);

    });
});

Route::post('/activate-employee-account', [EmployeeActivationController::class, 'activate']);