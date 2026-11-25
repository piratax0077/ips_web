<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateExternalToken
{
    public function handle(Request $request, Closure $next)
    {
        $expiresAt = (int) $request->session()->get('external_auth.expires_at', 0);

        if ($request->session()->has('external_auth.user') && $expiresAt > now()->timestamp) {
            return $next($request);
        }

        $request->session()->forget('external_auth');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        return redirect()->route('login')->with('auth_required', true);
    }
}
