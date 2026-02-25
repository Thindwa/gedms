<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    /**
     * Restrict admin routes to users with at least one admin permission.
     * Hides Administration from Director, Deputy Director, Minister, etc.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $hasAdminAccess = $user
            && ($user->can('manage-system')
                || $user->can('manage-department')
                || $user->can('manage-document-types')
                || $user->can('manage-sensitivity-levels')
                || $user->can('manage-workflows')
                || $user->can('manage-retention')
                || $user->can('manage-roles')
                || $user->can('view-audit-logs')
                || $user->can('view-audit-only'));

        return $hasAdminAccess ? $next($request) : abort(403, 'Administration access denied.');
    }
}
