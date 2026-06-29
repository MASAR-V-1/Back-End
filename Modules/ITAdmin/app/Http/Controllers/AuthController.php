<?php

namespace Modules\ITAdmin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * تسجيل دخول المستخدمين وإصدار Token عبر Sanctum.
     */
    public function login(Request $request)
    {
        // 1. التحقق من صحة المدخلات بصرامة
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'حقل البريد الإلكتروني إلزامي.',
            'email.email' => 'يجب إدخال بريد إلكتروني صحيح.',
            'password.required' => 'حقل كلمة المرور إلزامي.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // 2. البحث عن المستخدم (استخدام query() يحل مشكلة الخط الأحمر تحت where)
        $user = User::query()->where('email', $request->email)->first();

        // 3. التحقق من وجود المستخدم ومطابقة كلمة المرور وحالة الحساب
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات الاعتماد المدخلة غير صحيحة.',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'هذا الحساب معطل حالياً، يرجى مراجعة مسؤول النظام.',
            ], 403);
        }

        // 4. إنشاء الـ Token
        $token = $user->createToken('masar_auth_token')->plainTextToken;

        // 5. إرجاع الرد الناجح
        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'organization_id' => $user->organization_id,
                ],
            ],
        ], 200);
    }

    /**
     * تسجيل الخروج وإتلاف الـ Token الحالي.
     */
    public function logout(Request $request)
    {
        // جلب الـ Token الحالي كموديل شخصي مرجعي من قاعدة البيانات مباشرة
        // هذه الطريقة تقطع الشك باليقين ولا يمكن لـ VS Code الاعتراض عليها
        $token = $request->user()->currentAccessToken();

        if ($token) {
            /** @var PersonalAccessToken $token */
            $token->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الخروج بنجاح وإبطال الصلاحية.',
        ], 200);
    }

    public function acceptInvitationAndSetPassword(Request $request)
    {
        // 1. التحقق من المدخلات القادمة من شاشة React
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'كلمة المرور الجديدة حقل إلزامي.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 8 خانات لحماية الحساب.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // 2. التحقق من صحة وصلاحية التوكن في جدول الدعوات
        $invitation = DB::table('user_invitations')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (! $invitation) {
            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، هذا الرابط غير صالح، أو تم استخدامه مسبقاً.',
            ], 404);
        }

        // 3. التحقق من تاريخ انتهاء الصلاحية (24 ساعة)
        if (now()->greaterThan($invitation->expires_at)) {
            DB::table('user_invitations')->where('id', $invitation->id)->delete(); // تنظيف السجل المنتهي

            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، انتهت صلاحية هذا الرابط الأمنية (تجاوز 24 ساعة). يرجى طلب دعوة جديدة.',
            ], 410);
        }

        // 4. كل شيء سليم! نقوم الآن بإنشاء المستخدم الفعلي في قاعدة البيانات بكل أمان
        $user = User::create([
            'organization_id' => $invitation->organization_id, // العزل التلقائي للشركة
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'must_change_password' => false, // false لأنه اختارها بنفسه الآن عبر الرابط المشفر!
        ]);

        // 5. إسناد الدور له فوراً برمجياً
        try {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('IT_Admin');
            }
        } catch (\Exception $e) {
        }

        // 6. استهلاك وحذف الدعوة نهائياً لمنع إعادة استخدام الرابط
        DB::table('user_invitations')->where('id', $invitation->id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تهانينا! تم تعيين كلمة المرور الخاصة بك وتفعيل حسابك بنجاح. يمكنك الآن تسجيل الدخول والانتقال للوحة التحكم الخاصة بك.',
        ], 200);
    }
}
