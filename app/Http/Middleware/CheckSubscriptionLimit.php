<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\OrganizationService;

class CheckSubscriptionLimit
{
    public function __construct(private readonly OrganizationService $organizationService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->organizationService->checkSubscriptionLimit()) {
            abort(403, 'Subscription limits exceeded.');
        }

        return $next($request);
    }
}