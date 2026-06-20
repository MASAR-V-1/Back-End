<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
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

        // توليد باسورد عشوائي مؤقت (8 خانات)
        $temporaryPassword = Str::random(8);

        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($temporaryPassword),
            'organization_id' => $orgAdmin->organization_id,
            'is_active' => true,
            'must_change_password' => true,
            'email_verified_at' => now(), // الموظف ما يحتاج verification، org_admin أكد هويته
        ]);

        $employee->assignRole('employee');

        $employee->notify(new EmployeeAccountCreated($temporaryPassword));

        return response()->json([
            'message' => 'تم إنشاء حساب الموظف بنجاح وتم إرسال بيانات الدخول له عبر الإيميل.',
            'employee' => $employee,
        ], 201);
    }

    // عرض كل موظفي المؤسسة (لاحقًا، بس نضيفها هلأ كبونص بسيط)
    public function index(\Illuminate\Http\Request $request)
    {
        $orgAdmin = $request->user();

        $employees = User::where('organization_id', $orgAdmin->organization_id)
            ->role('employee')
            ->latest()
            ->paginate(15);

        return response()->json($employees);
    }
}
