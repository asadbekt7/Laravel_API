<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireCrudPermission
{
    public function handle(Request $request, Closure $next, string $resource): Response
    {
        /*if (!config('services.my_uwed.enabled') && !app()->isProduction()) {
            return $next($request);
        }

        $action = match (strtoupper($request->method())) {
            'POST'         => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE'       => 'delete',
            default        => 'view', // GET, HEAD
        };

        $permission = $resource.'.'.$action;

        if ($request->user()?->ssoClaims()?->hasPermission($permission)) {
            return $next($request);
        }

        return response()->json(['message' => "Bu amal uchun ruxsatingiz yo'q."], 403);*/

        return $next($request);
    }
}
