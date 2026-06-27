<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Notifications\OrganizationApproved;
use App\Notifications\OrganizationNeedsChanges;
use App\Notifications\OrganizationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending_review');

        $query = Organization::query()->with('users');

        if ($status === 'rejected') {
            $query->onlyTrashed()->where('status', Organization::STATUS_REJECTED);
        } elseif ($status === 'approved') {
            $query->where('status', Organization::STATUS_APPROVED);
        } elseif ($status === 'needs_changes') {
            $query->where('status', Organization::STATUS_NEEDS_CHANGES);
        } elseif ($status === 'all') {
            $query->withTrashed();
        } else {
            $query->where('status', Organization::STATUS_PENDING_REVIEW);
        }

        $organizations = $query->latest()->paginate(10)->withQueryString();

        return view('super_admin.organizations.index', compact('organizations', 'status'));
    }

    public function show(Organization $organization)
    {
        // تحميل الموظفين والمستخدمين المرتبطين بالمؤسسة لضمان جلب صلاحية الأدمن بسلاسة في الـ blade
        $organization->load('users');

        return view('super_admin.organizations.show', compact('organization'));
    }
    public function toggleActive(Organization $organization)
    {
        $owner = $organization->users?->first(fn($user) => $user->isOrgAdmin());

        if (!$owner) {
            flash()->error('تعذر العثور على مدير للمؤسسة لتعديل حالته.');
            return redirect()->back();
        }

        if ($organization->isSuspended()) {
            // إعادة تفعيل المؤسسة - ما بنلمس is_active تبع أي مستخدم فردي
            $organization->update(['suspended_at' => null]);
            $message = 'تم إلغاء تجميد المؤسسة. ملاحظة: الحسابات التي كانت معطّلة بشكل فردي من قبل المدير تبقى معطّلة.';
        } else {
            // تجميد المؤسسة - نحذف توكنات الكل فورًا (بغض النظر عن is_active الفردي)
            $organization->update(['suspended_at' => now()]);

            $tokenableIds = $organization->users()->pluck('id');
            \Laravel\Sanctum\PersonalAccessToken::where('tokenable_type', \App\Models\User::class)
                ->whereIn('tokenable_id', $tokenableIds)
                ->delete();

            $message = 'تم تجميد المؤسسة وجميع حساباتها مؤقتاً.';
        }

        flash()->success($message);

        return redirect()->back();
    }
    public function approve(Organization $organization)
    {
        $organization->update([
            'status' => Organization::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'review_notes' => null,
        ]);

        $orgAdmin = $organization->users()->first();
        $orgAdmin?->notify(new OrganizationApproved($organization));
        flash()->success('تم اعتماد المؤسسة بنجاح.');
        return redirect()->back();
    }

    // طلب تعديل - حقول محددة مع ملاحظات
    public function requestChanges(Request $request, Organization $organization)
    {
        $request->validate([
            'review_notes' => ['required', 'array', 'min:1'],
            'review_notes.*' => ['required', 'string', 'max:500'],
        ]);

        $organization->update([
            'status' => Organization::STATUS_NEEDS_CHANGES,
            'review_notes' => $request->review_notes, // array: ['region' => '...', 'organization_type' => '...']
        ]);

        $orgAdmin = $organization->users()->first();
        $orgAdmin?->notify(new OrganizationNeedsChanges($organization));
        flash()->success('تم إرسال طلب التعديل للمؤسسة بنجاح.');
        return redirect()->back();
    }

    public function reject(Request $request, Organization $organization)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $orgAdmin = $organization->users()->first();

        $organization->update([
            'status' => Organization::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($orgAdmin) {
            $orgAdmin->notify(new OrganizationRejected($organization, $request->rejection_reason));
        }

        $organization->users()->delete();
        $organization->delete();
        flash()->success('تم رفض المؤسسة وحذف حساباتها بنجاح.');
        return redirect()->back();
    }
}
