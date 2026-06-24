<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function resend(Request $request)
    {

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        // الموظفين عندهم نظام تفعيل مختلف، ما يستخدموا هذا الـ endpoint
        if ($user->isEmployee()) {
            return response()->json([
                'message' => 'يرجى التواصل مع إدارة مؤسستك لإعادة إرسال رابط التفعيل.',
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'هاد الحساب متحقق منه مسبقًا.',
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'تم إرسال إيميل التحقق من جديد، تحقق من بريدك.',
        ]);
    }


    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // تأكد إنه الهاش مطابق لإيميل المستخدم (نفس منطق Laravel الافتراضي)
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'رابط التحقق غير صالح.'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'تم تأكيد الإيميل مسبقًا.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'تم تأكيد الإيميل بنجاح.']);
    }
}
