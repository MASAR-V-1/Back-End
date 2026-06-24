<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EmployeeAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class EmployeeActivationController extends Controller
{
    public function activate(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->activation_token || !Hash::check($request->token, $user->activation_token)) {
            return response()->json([
                'message' => 'رابط التفعيل غير صالح.',
            ], 400);
        }

        if ($user->activation_token_expires_at && $user->activation_token_expires_at->isPast()) {
            return response()->json([
                'message' => 'انتهت صلاحية رابط التفعيل، يرجى التواصل مع إدارة مؤسستك.',
            ], 400);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'must_change_password' => false,
            'activation_token' => null,
            'activation_token_expires_at' => null,
        ])->save();

        return response()->json([
            'message' => 'تم تفعيل الحساب بنجاح، يمكنك تسجيل الدخول الآن.',
        ]);
    }

    public function resend(Request $request, User $employee)
    {
        $orgAdmin = $request->user();
        // تأكد إنه الموظف تابع لنفس مؤسسة الـ org_admin
        if ($employee->organization_id !== $orgAdmin->organization_id) {
            return response()->json(['message' => 'غير مصرح لك بهذا الإجراء.'], 403);
        }

        if (!$employee->isEmployee()) {
            return response()->json(['message' => 'هذا المستخدم ليس موظفًا.'], 400);
        }

        if ($employee->hasVerifiedEmail()) {
            return response()->json(['message' => 'تم تفعيل حساب هذا الموظف مسبقًا.'], 400);
        }

        $token = Str::random(60);

        $employee->forceFill([
            'activation_token' => Hash::make($token),
            'activation_token_expires_at' => now()->addHours(24),
        ])->save();

        $employee->notify(new EmployeeAccountCreated($token));

        return response()->json([
            'message' => 'تم إرسال رابط التفعيل من جديد.',
        ]);
    }
}
