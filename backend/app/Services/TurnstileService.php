<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileService
{
    private const string DUMMY_PASS_SECRET = '1x0000000000000000000000000000000AA';

    public function verify(string $token, ?string $ip): bool
    {
        $secret = (string) config('services.turnstile.secret');

        if ($token === '' || $secret === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Turnstile could not be reached.', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }

        if (! $response->successful()) {
            Log::warning('Turnstile rejected the verification request.', [
                'status' => $response->status(),
            ]);

            return false;
        }

        $result = $response->json();

        if (! is_array($result) || ($result['success'] ?? false) !== true) {
            Log::warning('Turnstile rejected the visitor.', [
                'errors' => is_array($result) ? ($result['error-codes'] ?? []) : [],
            ]);

            return false;
        }

        if ($secret === self::DUMMY_PASS_SECRET) {
            return true;
        }

        $hostname = (string) config('services.turnstile.hostname');

        return $hostname !== ''
            && ($result['hostname'] ?? null) === $hostname
            && ($result['action'] ?? null) === 'contact';
    }
}
