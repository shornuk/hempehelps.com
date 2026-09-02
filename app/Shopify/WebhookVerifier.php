<?php

namespace App\Shopify;

use App\Shopify\Exceptions\ShopifyException;
use Illuminate\Http\Request;

class WebhookVerifier
{
    public function verify(Request $request): void
    {
        $secret = (string) config('shopify.webhook_secret');

        if ($secret === '') {
            throw new ShopifyException('SHOPIFY_WEBHOOK_SECRET is not configured.');
        }

        $hmac = (string) $request->header('X-Shopify-Hmac-Sha256');
        $computed = base64_encode(hash_hmac('sha256', $request->getContent(), $secret, true));

        if ($hmac === '' || ! hash_equals($computed, $hmac)) {
            throw new ShopifyException('Invalid Shopify webhook signature.');
        }

        $header = $request->header('X-Shopify-Triggered-At', (string) time());
        $timestamp = is_numeric($header) ? (int) $header : (int) strtotime((string) $header);

        if ($timestamp <= 0 || abs(time() - $timestamp) > (int) config('shopify.webhook_replay_window')) {
            throw new ShopifyException('Shopify webhook replay window exceeded.');
        }
    }
}
