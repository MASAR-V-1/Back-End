<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\BaseController;
use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // employee تابع لمؤسسة org_admin، فبنفحص نفس الشرط
        $organization = $user->organization;

        if (!$organization || $organization->status !== Organization::STATUS_APPROVED) {
            return BaseController::sendError('لا يمكنك الوصول لهذه الميزة قبل اعتماد حساب مؤسستك من الإدارة.', [
                'organization_status' => $organization?->status,
            ], 403);
        }

        return $next($request);
    }
}
