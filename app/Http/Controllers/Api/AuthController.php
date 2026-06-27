<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'admin_email' => ['required', 'email'],
            'admin_password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->admin_email)->first();

        if (!$user || !Hash::check($request->admin_password, $user->password)) {
            throw ValidationException::withMessages([
                'admin_email' => ['البيانات المدخلة غير صحيحة.'],
            ]);
        }

        // تحقق من تفعيل الإيميل (مهم لـ org_admin خاصة)
        if (!$user->hasVerifiedEmail()) {
            return BaseController::sendError('يرجى تأكيد بريدك الإلكتروني أولاً.', [], 403);
        }
        // تحقق إذا الحساب معطل (is_active)
        if (!$user->is_active) {
            return BaseController::sendError('تم تجميد حسابك، يرجى التواصل مع إدارة مؤسستك.', [], 403);
        }

        if ($user->organization && $user->organization->isSuspended()) {
            return BaseController::sendError('تم تجميد مؤسستك من قبل الإدارة، يرجى التواصل معهم.', [], 403);
        }
        // تحقق من حالة المؤسسة (لو org_admin)
        if ($user->isOrgAdmin() && $user->organization) {
            if ($user->organization->status === Organization::STATUS_REJECTED) {
                return BaseController::sendError('تم رفض طلب تسجيل مؤسستك بشكل نهائي.', [
                    'rejection_reason' => $user->organization->rejection_reason,
                ], 403);
            }
        }



        // حذف التوكنات القديمة (اختياري - يعني تسجيل دخول واحد بنفس الوقت فقط)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return BaseController::sendResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'organization_id' => $user->organization_id,
                'organization_status' => $user->organization?->status,
                'must_change_password' => $user->must_change_password,
            ],
            'token' => $token,
        ], 'تم تسجيل الدخول بنجاح.');
    }

    public function logout(Request $request)
    {
        // يحذف بس التوكن الحالي المستخدم بالـ request
        $request->user()->currentAccessToken()->delete();

        return BaseController::sendResponse([], 'تم تسجيل الخروج بنجاح.');
    }

    public function refresh(Request $request)
    {
        $user = $request->user();

        // نحذف التوكن القديم وننشئ جديد
        $request->user()->currentAccessToken()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return BaseController::sendResponse([
            'token' => $token,
        ], 'تم تحديث التوكن بنجاح.');
    }

    public function me(Request $request)
    {
        return BaseController::sendResponse([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->getRoleNames()->first(),
                'organization_id' => $request->user()->organization_id,
                'organization_status' => $request->user()->organization?->status,
                'must_change_password' => $request->user()->must_change_password,
            ],
        ], 'تم جلب بيانات المستخدم بنجاح.');
    }
}
