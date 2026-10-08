<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_admin) {
            abort(403, 'غير مصرح لك بالوصول.');
        }

        if ($permission === 'super' && ! $user->isSuperAdmin()) {
            abort(403, 'هذه الصفحة مخصصة للمدير الرئيسي (Super Admin) فقط.');
        }

        if ($permission !== 'super' && ! $user->canAccess($permission)) {
            abort(403, "ليس لديك صلاحية الوصول إلى قسم: {$permission}.");
        }

        return $next($request);
    }
}
