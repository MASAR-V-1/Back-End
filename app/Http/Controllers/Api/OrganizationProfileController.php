<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrganizationProfileRequest;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizationProfileController extends Controller
{
    // يرجع كل البيانات (القديمة + الجديدة) عشان الفرونت يعرضها بفورم واحد
    public function show(Request $request)
    {
        $user = $request->user();
        $organization = $user->organization;

        return BaseController::sendResponse([
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'description' => $organization->description,
                'region' => $organization->region,
                'organization_type' => $organization->organization_type,
                'status' => $organization->status,
                'review_notes' => $organization->review_notes,
                'rejection_reason' => $organization->rejection_reason,
            ],
            'admin' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'can_edit_main_fields' => $organization->canEditMainFields(),
            'can_edit_minor_fields' => $organization->canEditMinorFields(),
            'missing_fields' => $organization->missingRequiredFields($user),
        ], 'تم جلب بيانات المؤسسة بنجاح.');
    }

    // تحديث جزئي - org_admin يقدر يعبي حقل حقل
    public function update(UpdateOrganizationProfileRequest $request)
    {
        $user = $request->user();
        $organization = $user->organization;

        DB::transaction(function () use ($request, $user, $organization) {
            // الحقول الرئيسية - بس لو مسموح (الـ Request أصلًا تحقق من هذا، هنا ثاني حماية)
            if ($organization->canEditMainFields()) {
                if ($request->filled('admin_name')) {
                    $user->update(['name' => $request->admin_name]);
                }

                $organization->update(array_filter([
                    'name' => $request->organization_name,
                    'region' => $request->organization_region,
                    'organization_type' => $request->organization_type,
                ], fn($value) => !is_null($value)));
            }

            // الحقول الثانوية - مستقلة عن حالة canEditMainFields
            if ($organization->canEditMinorFields()) {
                $organization->update(array_filter([
                    'phone' => $request->organization_phone,
                    'description' => $request->organization_description,
                ], fn($value) => !is_null($value)));
            }
        });

        return BaseController::sendResponse([
            'organization' => $organization->fresh(),
            'missing_fields' => $organization->missingRequiredFields($user->fresh()),
        ], 'تم تحديث البيانات بنجاح.');
    }

    public function submitForReview(Request $request)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization->canEditProfile()) {
            return BaseController::sendError('لا يمكنك إرسال طلب المراجعة في هذه الحالة.', [], 403);
        }

        $missing = $organization->missingRequiredFields($user);

        if (!empty($missing)) {
            return BaseController::sendError('يرجى إكمال جميع البيانات المطلوبة قبل إرسال الطلب.', ['missing_fields' => $missing], 422);
        }

        $organization->update([
            'status' => Organization::STATUS_PENDING_REVIEW,
            'review_notes' => null,
        ]);

        $superAdmins = \App\Models\User::role('super_admin')->get();
        \Illuminate\Support\Facades\Notification::send(
            $superAdmins,
            new \App\Notifications\NewOrganizationRegistered($organization)
        );

        return BaseController::sendResponse([
            'message' => 'تم إرسال طلبك للمراجعة بنجاح.',
        ], 'تم إرسال طلبك للمراجعة بنجاح.');
    }
}

