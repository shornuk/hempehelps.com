<?php

namespace App\Auth\Actions;

use App\Auth\Jobs\SendMagicLinkJob;
use App\Auth\Models\MagicLinkToken;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class SendMagicLink
{
    public function handle(string $email, ?string $ip = null): MagicLinkToken
    {
        $key = 'otp:'.strtolower($email).'|'.$ip;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many login attempts. Try again shortly.',
            ]);
        }

        RateLimiter::hit($key, 600);

        $token = MagicLinkToken::issue($email, $ip);

        SendMagicLinkJob::dispatch($token->id);

        return $token;
    }
}
