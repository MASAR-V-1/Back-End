<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    // الخطوة 1: إرسال رابط إعادة تعيين الباسورد
    public function sendResetLink(Request $request)  
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink(
            $request->only('email')
        );
        // هاي
        if ($status === Password::RESET_LINK_SENT) {
            return BaseController::sendResponse([], 'تم إرسال رابط إعادة تعيين كلمة السر إلى بريدك.');
        }

        return BaseController::sendError('حدث خطأ، تأكد من صحة البريد الإلكتروني.', [], 400);
    }

    // الخطوة 2: تحديد الباسورد الجديد فعليًا
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'must_change_password' => false, // لو كان موظف مفروض يغير باسورد، هلأ خلص
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return BaseController::sendResponse([], 'تم تغيير كلمة السر بنجاح.');
        }

        return BaseController::sendError('الرابط غير صالح أو منتهي الصلاحية.', [], 400);
    }
}
