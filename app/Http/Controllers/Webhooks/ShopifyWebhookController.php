<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Seo\Jobs\ProcessShopifyWebhookJob;
use App\Seo\WebhookEvent;
use App\Shopify\Exceptions\ShopifyException;
use App\Shopify\WebhookVerifier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ShopifyWebhookController extends Controller
{
    public function __invoke(string $topic, Request $request, WebhookVerifier $verifier): Response
    {
        try {
            $verifier->verify($request);
        } catch (ShopifyException) {
            return response('Invalid signature', 401);
        }

        $hash = hash('sha256', $topic.'|'.$request->getContent());

        $event = WebhookEvent::query()->firstOrCreate(
            ['payload_hash' => $hash],
            [
                'source' => 'shopify',
                'topic' => $topic,
                'external_id' => $request->header('X-Shopify-Webhook-Id', Str::uuid()->toString()),
            ],
        );

        if ($event->processed_at === null) {
            ProcessShopifyWebhookJob::dispatch($event->id, $topic, $request->all());
        }

        return response('ok', 200);
    }
}
