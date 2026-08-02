<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $organizationId = session('current_organization_id');

            if (!$organizationId) {
                // Determine a default organization if none in session
                // $organizationId = ...
            }

            if (!$organizationId && !$user->is_super_admin) {
                abort(403, 'No active organization context found.');
            }

            if ($organizationId) {
                app()->singleton('current_organization_id', fn() => $organizationId);
            }
        }

        return $next($request);
    }
}