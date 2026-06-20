<?php

use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\OrganizationRegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register-organization', [OrganizationRegistrationController::class, 'store']);
Route::post('/email/resend-verification', [EmailVerificationController::class, 'resend'])
    ->middleware('throttle:3,1'); // 3 محاولات بالدقيقة بس

Route::middleware(['auth:sanctum', 'role:org_admin'])->group(function () {
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees', [EmployeeController::class, 'index']);
});
