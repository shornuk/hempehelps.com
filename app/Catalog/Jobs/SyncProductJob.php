<?php

namespace App\Catalog\Jobs;

use App\Catalog\Models\ShopifyProduct;
use App\Catalog\Models\ShopifyVariant;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncProductJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(public array $payload) {}

    public function uniqueId(): string
    {
        return (string) ($this->payload['admin_graphql_api_id'] ?? $this->payload['id'] ?? uniqid());
    }

    public function handle(): void
    {
        $shopifyId = isset($this->payload['admin_graphql_api_id'])
            ? (string) $this->payload['admin_graphql_api_id']
            : 'gid://shopify/Product/'.$this->payload['id'];

        $product = ShopifyProduct::query()->updateOrCreate(
            ['shopify_id' => $shopifyId],
            [
                'handle' => $this->payload['handle'],
                'title' => $this->payload['title'],
                'status' => $this->payload['status'] ?? 'active',
                'product_type' => $this->payload['product_type'] ?? '',
                'vendor' => $this->payload['vendor'] ?? '',
                'tags' => array_values(array_filter(array_map(trim(...), explode(',', (string) ($this->payload['tags'] ?? ''))))),
                'featured_image_url' => $this->payload['image']['src'] ?? null,
                'published_at' => $this->payload['published_at'] ?? null,
                'synced_at' => now(),
            ],
        );

        foreach ($this->payload['variants'] ?? [] as $index => $variant) {
            ShopifyVariant::query()->updateOrCreate(
                ['shopify_id' => isset($variant['admin_graphql_api_id'])
                    ? (string) $variant['admin_graphql_api_id']
                    : 'gid://shopify/ProductVariant/'.$variant['id']],
                [
                    'product_id' => $product->id,
                    'sku' => $variant['sku'] ?? '',
                    'option_values' => array_values(array_filter([
                        $variant['option1'] ?? null,
                        $variant['option2'] ?? null,
                        $variant['option3'] ?? null,
                    ])),
                    'position' => $variant['position'] ?? $index,
                ],
            );
        }

        ForgetProductCacheJob::dispatch($product->handle);
    }
}
