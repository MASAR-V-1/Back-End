<?php

namespace Modules\ITAdmin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    /**
     * عرض بيانات الشركة/المنظمة المسجلة الحالية الحاملة للتوكن.
     */
    public function show(Request $request)
    {
        // استخراج معرف المنظمة من المستخدم الحركي الحالي
        $organizationId = $request->user()->organization_id;

        // جلب بيانات المنظمة مباشرة من قاعدة البيانات
        $organization = DB::table('organizations')->where('id', $organizationId)->first();

        if (!$organization) {
            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، لم يتم العثور على بيانات المنظمة المرتبطة بهذا الحساب.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $organization
        ], 200);
    }

    /**
     * تحديث بيانات الشركة/المنظمة الحالية من قبل الإدارة.
     */
    public function update(Request $request)
    {
        $organizationId = $request->user()->organization_id;

        // 1. التحقق من صحة البيانات المدخلة لتحديث الملف الشخصي للشركة
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:organizations,email,' . $organizationId,
        ], [
            'name.required'  => 'اسم المنظمة حقل إلزامي.',
            'email.required' => 'البريد الإلكتروني العام للمنظمة حقل إلزامي.',
            'email.unique'   => 'هذا البريد الإلكتروني مستخدم بالفعل من قبل منظمة أخرى.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. تحديث السجل بأمان تام وعزل كامل
        DB::table('organizations')
            ->where('id', $organizationId)
            ->update([
                'name'       => $request->name,
                'email'      => $request->email,
                'updated_at' => now(),
            ]);

        // جلب السجل المحدث لإعادة عرضه
        $updatedOrganization = DB::table('organizations')->where('id', $organizationId)->first();

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تحديث ملف بيانات المنظمة بنجاح.',
            'data'    => $updatedOrganization
        ], 200);
    }
}
