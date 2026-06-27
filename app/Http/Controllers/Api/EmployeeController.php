<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\User;
use App\Notifications\EmployeeAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function store(CreateEmployeeRequest $request)
    {
        $orgAdmin = $request->user();
        $token = Str::random(60);
        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(32)), // باسورد عشوائي مؤقت، ما رح يستخدمه أصلًا
            'organization_id' => $orgAdmin->organization_id,
            'is_active' => true,
            'must_change_password' => true,
            'email_verified_at' => null, // غير مفعّل لحد ما يفعّل حسابه
            'activation_token' => Hash::make($token), // نخزنه مشفر، أمان أكتر
            'activation_token_expires_at' => now()->addHours(24),
        ]);

        $employee->assignRole('employee');

        $employee->notify(new EmployeeAccountCreated($token)); // التوكن الخام (plain) بيروح بالإيميل بس

        return BaseController::sendResponse([
            'employee' => new EmployeeResource($employee),
        ], 'تم إنشاء حساب الموظف بنجاح وتم إرسال رابط تفعيل الحساب له عبر الإيميل.', 201);
    }

    // عرض كل موظفي المؤسسة (لاحقًا، بس نضيفها هلأ كبونص بسيط)
    public function index(\Illuminate\Http\Request $request)
    {
        $orgAdmin = $request->user();

        $employees = User::where('organization_id', $orgAdmin->organization_id)
            ->role('employee')
            ->latest()
            ->paginate(15);

        return BaseController::sendResponse([
            'employees' => EmployeeResource::collection($employees),
        ], 'تم جلب قائمة الموظفين بنجاح.');
    }
}
