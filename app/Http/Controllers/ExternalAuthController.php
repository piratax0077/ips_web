<?php

namespace App\Http\Controllers;

use App\Services\ExternalTokenAuthenticator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExternalAuthController extends Controller
{
    public function exchange(Request $request, ExternalTokenAuthenticator $authenticator): JsonResponse
    {
        $request->validate(['token' => ['required', 'string', 'max:4096']]);

        $user = $authenticator->validate((string) $request->input('token'));

        if (! $user) {
            return response()->json(['message' => 'Token inválido o vencido.'], 401);
        }

        $request->session()->regenerate();
        $request->session()->put([
            'external_auth.user' => $user,
            'external_auth.validated_at' => now()->timestamp,
            'external_auth.expires_at' => now()->addMinutes(
                (int) config('services.ips_auth.session_ttl', 120)
            )->timestamp,
        ]);

        $request->session()->regenerateToken();

        return response()->json(['authenticated' => true, 'user' => $user]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['authenticated' => false]);
    }
}
