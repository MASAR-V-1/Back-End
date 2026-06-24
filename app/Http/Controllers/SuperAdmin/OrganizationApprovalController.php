<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Notifications\OrganizationApproved;
use App\Notifications\OrganizationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationApprovalController extends Controller
{
    // عرض كل المؤسسات المعلقة
    public function index(Request $request)
    {

        $status = $request->query('status', 'pending'); // افتراضي: pending

        $query = Organization::query()->with('users');

        if ($status === 'rejected') {
            $query->onlyTrashed()->where('status', 'rejected');
        } elseif ($status === 'approved') {
            $query->where('status', 'approved')->with('approver');
        } elseif ($status === 'all') {
            $query->with('approver')->withTrashed();
        } else {
            // pending (افتراضي) - بس الي تحقق إيميلهم
            $query->where('status', 'pending')
                ->whereHas('users', function ($q) {
                    $q->whereNotNull('email_verified_at');
                });
        }

        $organizations = $query->latest()->paginate(10)->withQueryString();

        return view('super_admin.organizations.index', compact('organizations', 'status'));
    }

    // الموافقة على مؤسسة
    public function approve(Organization $organization)
    {
        $organization->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::user()->id,
        ]);

        //  ممكن نرسل إشعار للـ org_admin إنه تمت الموافقة
        $orgAdmin = $organization->users()->first();
        if ($orgAdmin) {
            $orgAdmin->notify(new OrganizationApproved($organization));
        }

        flash()->success('تمت الموافقة على المؤسسة بنجاح.');
        return redirect()->back();
    }

    // رفض مؤسسة
    public function reject(Request $request, Organization $organization)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $orgAdmin = $organization->users()->first();
        
        $organization->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
          //  إرسال إشعار/إيميل للمتقدم بسبب الرفض
        // نرسل الإشعار قبل الحذف (soft delete)، لأنه بعد الحذف بصير notifiable محذوف منطقيًا
        if ($orgAdmin) {
            $orgAdmin->notify(new OrganizationRejected($organization, $request->rejection_reason));
        }
        // حذف المستخدم org_admin المرتبط (عشان يقدر يسجل من جديد بنفس الإيميل)
        // Soft delete للـ user والـ organization (بضلوا بالسجل التاريخي)
        $organization->users()->delete();
        $organization->delete();

        flash()->success('تم رفض الطلب.');
        return redirect()->back();
    }
}
