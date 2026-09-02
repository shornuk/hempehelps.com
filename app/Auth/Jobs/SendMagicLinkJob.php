<?php

namespace App\Auth\Jobs;

use App\Auth\Mail\MagicLinkMail;
use App\Auth\Models\MagicLinkToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMagicLinkJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $tokenId) {}

    public function handle(): void
    {
        $token = MagicLinkToken::query()->find($this->tokenId);

        if (! $token instanceof MagicLinkToken) {
            return;
        }

        $url = url('/login/verify?token='.$token->token.'&email='.urlencode($token->email));

        if (app()->environment('local', 'testing')) {
            Log::info('Magic link issued', ['email' => $token->email, 'url' => $url]);
        }

        Mail::to($token->email)->send(new MagicLinkMail($url));
    }
}
