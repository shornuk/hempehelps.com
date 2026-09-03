<?php

namespace Tests\Feature;

use App\Catalog\Models\ShopifyProduct;
use App\Seo\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_invalid_hmac(): void
    {
        config(['shopify.webhook_secret' => 'whsec-test']);

        $this->call('POST', '/webhooks/shopify/products/update', server: [
            'HTTP_X-Shopify-Hmac-Sha256' => 'nope',
            'HTTP_X-Shopify-Triggered-At' => (string) time(),
            'CONTENT_TYPE' => 'application/json',
        ], content: '{"id":1}')->assertStatus(401);
    }

    public function test_is_idempotent_on_payload_hash(): void
    {
        config(['shopify.webhook_secret' => 'whsec-test']);

        $body = json_encode([
            'id' => 11,
            'admin_graphql_api_id' => 'gid://shopify/Product/11',
            'handle' => 'hot-gel',
            'title' => 'Hot Gel',
            'status' => 'active',
            'variants' => [],
        ], JSON_THROW_ON_ERROR);
        $hmac = base64_encode(hash_hmac('sha256', $body, 'whsec-test', true));

        $server = [
            'HTTP_X-Shopify-Hmac-Sha256' => $hmac,
            'HTTP_X-Shopify-Triggered-At' => (string) time(),
            'CONTENT_TYPE' => 'application/json',
        ];

        $this->call('POST', '/webhooks/shopify/products/update', server: $server, content: $body)->assertOk();
        $this->call('POST', '/webhooks/shopify/products/update', server: $server, content: $body)->assertOk();

        $this->assertSame(1, WebhookEvent::query()->count());
        $this->assertSame(1, ShopifyProduct::query()->where('handle', 'hot-gel')->count());
    }
}
