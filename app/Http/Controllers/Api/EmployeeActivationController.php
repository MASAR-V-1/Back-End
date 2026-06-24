<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
}
