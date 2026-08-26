<?php

namespace App\Http\Middleware;

use App\Auth\MyUwedTokenVerifier;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MyUwedAuth
{
    private const UNSIGNED_QUERY = ['lang'];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasValidSignatureWhileIgnoring(self::UNSIGNED_QUERY)) {
            return $next($request);
        }

        $enabled = (bool) config('services.my_uwed.enabled');

        if (! $enabled && ! app()->isProduction()) {
            return $next($request);
        }

        $secret = config('services.my_uwed.secret');

        if (empty($secret)) {
            Log::error('MY_UWED_JWT_SECRET sozlanmagan.');

            return response()->json(['message' => 'Server auth sozlanmagan.'], 500);
        }

        $token = $this->extractToken($request);

        if (empty($token)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $verifier = new MyUwedTokenVerifier($secret, config('services.my_uwed.issuer'));
            $claims = $verifier->verify($token);
        } catch (\Throwable $e) {
            Log::warning('my.uwed.uz token rad etildi: '.$e::class.': '.$e->getMessage().' ['.$request->path().']');

            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $user = User::syncFromSso($claims);

        Auth::setUser($user);

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $bearer = $request->bearerToken();
        if (! empty($bearer)) {
            return $bearer;
        }

        $cookieName = config('services.my_uwed.cookie');
        if (! empty($cookieName)) {
            $cookie = $request->cookie($cookieName);

            return is_string($cookie) ? $cookie : null;
        }

        return null;
    }
}
