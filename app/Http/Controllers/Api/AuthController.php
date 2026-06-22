<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            return response()->json([
                'message' => 'يرجى تأكيد بريدك الإلكتروني أولاً.',
            ], 403);
        }

        // تحقق من حالة المؤسسة (لو org_admin)
        if ($user->isOrgAdmin() && $user->organization) {
            if ($user->organization->status === 'pending') {
                return response()->json([
                    'message' => 'طلبك قيد المراجعة من الإدارة، يرجى الانتظار.',
                ], 403);
            }

            if ($user->organization->status === 'rejected') {
                return response()->json([
                    'message' => 'تم رفض طلب تسجيل مؤسستك.',
                ], 403);
            }
        }

        // تحقق إذا الحساب معطل (is_active)
        if (!$user->is_active) {
            return response()->json([
                'message' => 'هذا الحساب معطل، يرجى التواصل مع الإدارة.',
            ], 403);
        }

        // حذف التوكنات القديمة (اختياري - يعني تسجيل دخول واحد بنفس الوقت فقط)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'organization_id' => $user->organization_id,
                'must_change_password' => $user->must_change_password,
            ],
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // يحذف بس التوكن الحالي المستخدم بالـ request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح.',
        ]);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();

        // نحذف التوكن القديم وننشئ جديد
        $request->user()->currentAccessToken()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
