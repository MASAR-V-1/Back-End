<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\BaseController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // الشرط 1: المستخدم نفسه معطل فرديًا (من org_admin أو سوبر ادمن)
        if (!$user->is_active) {
            $request->user()->currentAccessToken()->delete();
            return BaseController::sendError('تم تجميد حسابك، يرجى التواصل مع إدارة مؤسستك.', [], 403);
        }

        // الشرط 2: المؤسسة كاملة معلّقة من السوبر ادمن
        if ($user->organization && $user->organization->isSuspended()) {
            $request->user()->currentAccessToken()->delete();

            return BaseController::sendError('تم تجميد مؤسستك من قبل الإدارة، يرجى التواصل معهم.', [], 403);
        }

        return $next($request);
    }
}
