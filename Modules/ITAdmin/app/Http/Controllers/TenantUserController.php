<?php

namespace Modules\ITAdmin\app\Http\Controllers; // تأكيد وجود app هنا

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\ITAdmin\app\Emails\ITAdminWelcomeMail;

class TenantUserController extends Controller
{
    /**
     * جلب جميع مستخدمي وموظفي المنظمة التي ينتمي إليها المسؤول الحالي.
     */
    public function index(Request $request)
    {
        // جلب معرف المنظمة الصحيح من حساب المستخدم الحالي
        $organizationId = $request->user()->organization_id;

        // تعديل الاستعلام ليتطابق مع اسم العمود في قاعدة بياناتك (organization_id)
        $users = User::query()->where('organization_id', $organizationId)->get();

        return response()->json([
            'status' => 'success',
            'count' => $users->count(),
            'data' => $users,
        ], 200);
    }

    public function store(Request $request)
    {
        // 1. التحقق الصارم من المدخلات (قمنا بإزالة حقل password لأنه سيولد تلقائياً وعشوائياً الآن)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
        ], [
            'name.required' => 'اسم الموظف حقل إلزامي.',
            'email.required' => 'البريد الإلكتروني حقل إلزامي.',
            'email.email' => 'يرجى إدخال بريد إلكتروني صالح.',
            'email.unique' => 'هذا البريد الإلكتروني مسجل مسبقاً في النظام لمستند آخر.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // 2. استخراج معرف المنظمة الصحيح من المسؤول الحالي لضمان العزل والأمان
        $organizationId = $request->user()->organization_id;

        // 2. التحقق من عدم وجود دعوة معلقة لنفس البريد لتفادي التكرار
        $existingInvitation = DB::table('user_invitations')->where('email', $request->email)->first();
        if ($existingInvitation) {
            return response()->json([
                'status' => 'error',
                'message' => 'توجد بالفعل دعوة معلقة أُرسلت لهذا البريد الإلكتروني مسبقاً.',
            ], 400);
        }

        // 3. توليد توكن أمني معقد وتحديد صلاحيته بـ 24 ساعة
        $token = Str::random(40);
        $expiresAt = now()->addHours(24);

        // 4. حفظ بيانات الدعوة في الجدول الجديد
        DB::table('user_invitations')->insert([
            'organization_id' => $organizationId,
            'email' => $request->email,
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. بناء الرابط السحري الذي سينتقل لـ React (قم بتعديل الدومين حسب إعدادات فرونت إند)
        // نمرر الاسم في الرابط أيضاً لتسهيل قراءته في واجهة React الترحيبية
        $invitationUrl = 'http://localhost:3000/set-password?token='.$token.'&email='.urlencode($request->email).'&name='.urlencode($request->name);

        // 6. إرسال البريد الإلكتروني المحمي بالرابط فقط
        try {
            Mail::to($request->email)->send(new ITAdminWelcomeMail($request->name, $request->email, $invitationUrl));
            $mailStatus = 'تم إرسال رابط التعيين الآمن بنجاح للبريد الإلكتروني.';
        } catch (\Exception $e) {
            $mailStatus = 'تم حفظ الدعوة بنجاح، ولكن فشل إرسال البريد بسبب إعدادات مخدم SMTP.';
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء دعوة الموظف الجديد وتأمينها برمجياً.',
            'mail_info' => $mailStatus,
            'debug_data' => [
                'token' => $token,
                'invitation_url' => $invitationUrl, // نمرره هنا لتختبره مباشرة في Postman وضغط الرابط للتأكد!
            ],
        ], 201);
    }
}
