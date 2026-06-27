<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\NewOrganizationRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class OrganizationRegistrationController extends Controller
{
    public function store(RegisterOrganizationRequest $request)
    {
        $validated = $request->validated();

        // نرجع array فيه الاثنين (user + organization) من الـ transaction
        $result = DB::transaction(function () use ($validated) {

            $organization = Organization::create([
                'name' => $validated['organization_name'],
                'email' => $validated['organization_email'],
                'phone' => $validated['organization_phone'] ?? null,
                'description' => $validated['organization_description'] ?? null,
                'region' => $validated['organization_region'] ?? null,
                'organization_type' => $validated['organization_type'] ?? null,
                'agreed_to_terms' => $validated['agreed_to_terms'],
                'status' => Organization::STATUS_INCOMPLETE,
            ]);

            $user = User::create([
                'name' => $validated['admin_name'] ?? null,
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'organization_id' => $organization->id,
                'is_active' => true,
            ]);

            $user->assignRole('org_admin');

            return [
                'user' => $user,
                'organization' => $organization,
            ];
        });

        $user = $result['user'];
        $organization = $result['organization'];

        // 4. إرسال إيميل تحقق (verification email) - Laravel الافتراضي
        event(new \Illuminate\Auth\Events\Registered($user));

        // 5. إرسال إشعار للسوبر ادمن (رح نفصلها بخطوة جاية)
        // بعتناه عن طريق الـ event listener SendOrganizationNotificationToSuperAdmins
        // $superAdmins = User::role('super_admin')->get();
        // Notification::send($superAdmins, new NewOrganizationRegistered($organization));

        return BaseController::sendResponse(['organization' => $organization], 'تم تسجيل حسابك بنجاح. تحقق من إيميلك لتأكيد الحساب، ثم أكمل بيانات مؤسستك.', 201);
    }
}
