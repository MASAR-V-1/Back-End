<?php

use Illuminate\Support\Facades\Route;
use Modules\ITAdmin\app\Http\Controllers\AuthController;
use Modules\ITAdmin\app\Http\Controllers\OrganizationController;
use Modules\ITAdmin\app\Http\Controllers\TenantUserController;

/*
|--------------------------------------------------------------------------
| ITAdmin Module API Routes
|--------------------------------------------------------------------------
*/

// مسار عام: تسجيل الدخول لا يتطلب توكن مسبق
Route::post('/it-admin/login', [AuthController::class, 'login']);

// المسار العام الذي سيستدعيه مطور React عندما يضغط الموظف على رابط الإيميل
Route::post('/tenant/accept-invitation', [AuthController::class, 'acceptInvitationAndSetPassword']);

// مسارات محمية: تتطلب التمرير عبر حارس المصادقة لارافيل سانكتوم (Sanctum Middleware)
Route::middleware('auth:sanctum')->group(function () {

    // تسجيل الخروج وإتلاف الـ Token الحالي
    Route::post('/it-admin/logout', [AuthController::class, 'logout']);

    // 1. واجهة الشركة (إدارة ملف وبينات المنظمة المسجلة)
    Route::get('/tenant/profile', [OrganizationController::class, 'show']);
    Route::put('/tenant/profile', [OrganizationController::class, 'update']);

    // جلب قائمة موظفي المنظمة الحالية حصراً
    Route::get('/tenant/users', [TenantUserController::class, 'index']);

    // إنشاء موظف جديد داخل المنظمة الحالية وفرض الـ tenant_id تلقائياً
    Route::post('/tenant/users', [TenantUserController::class, 'store']);

});
