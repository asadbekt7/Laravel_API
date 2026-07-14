<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!config('services.my_uwed.enabled') && !app()->isProduction()) {
            return $next($request);
        }

        $claims = $request->user()?->ssoClaims();

        // Yozilgan ruxsatlardan bittasi bo'lsa yetarli.
        foreach ($permissions as $permission) {
            if ($claims?->hasPermission($permission)) {
                return $next($request);
            }
        }

        return response()->json(['message' => "Bu amal uchun ruxsatingiz yo'q."], 403);
    }
}
