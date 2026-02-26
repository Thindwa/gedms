<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    /**
     * Grant admin-area access to any user holding a manage-* or view-audit*
     * permission. New permissions following this convention automatically
     * grant admin access without code changes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Administration access denied.');
        }

        $adminPermissions = Permission::where('guard_name', 'web')
            ->where(fn ($q) => $q->where('name', 'like', 'manage-%')
                                 ->orWhere('name', 'like', 'view-audit%'))
            ->pluck('name');

        $hasAdminAccess = $adminPermissions->contains(fn ($perm) => $user->can($perm));

        return $hasAdminAccess ? $next($request) : abort(403, 'Administration access denied.');
    }
}
