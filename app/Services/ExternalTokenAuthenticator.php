<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExternalTokenAuthenticator
{
    public function validate(string $token): ?array
    {
        if ($token === '') {
            return null;
        }

        try {
            $response = Http::acceptJson()
                ->withToken($token)
                ->timeout((int) config('services.ips_auth.timeout', 8))
                ->post(rtrim(config('services.ips_auth.url'), '/').'/api/get-user', [
                    'token' => $token,
                ]);
        } catch (Throwable $exception) {
            Log::warning('No fue posible validar el token con IPS Auth.', [
                'exception' => get_class($exception),
            ]);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $user = $response->json('user');

        return is_array($user) && ! empty($user['id']) ? $user : null;
    }
}
