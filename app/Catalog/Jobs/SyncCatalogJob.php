<?php

namespace App\Catalog\Jobs;

use App\Catalog\Models\ShopifyCollection;
use App\Catalog\Models\ShopifyProduct;
use App\Catalog\Models\ShopifyVariant;
use App\Shopify\AdminClient;
use App\Shopify\Exceptions\MissingCredentialsException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncCatalogJob implements ShouldQueue
{
    use Queueable;

    public function handle(AdminClient $admin): void
    {
        try {
            $data = $admin->query($admin->document('products'), ['cursor' => null]);
        } catch (MissingCredentialsException $exception) {
            Log::warning($exception->getMessage());

            return;
        }

        foreach ($data['products']['nodes'] ?? [] as $node) {
            $product = ShopifyProduct::query()->updateOrCreate(
                ['shopify_id' => $node['id']],
                [
                    'handle' => $node['handle'],
                    'title' => $node['title'],
                    'status' => strtolower((string) ($node['status'] ?? 'active')),
                    'product_type' => $node['productType'] ?? '',
                    'vendor' => $node['vendor'] ?? '',
                    'tags' => $node['tags'] ?? [],
                    'featured_image_url' => $node['featuredImage']['url'] ?? null,
                    'published_at' => $node['publishedAt'] ?? null,
                    'synced_at' => now(),
                ],
            );

            foreach ($node['variants']['nodes'] ?? [] as $index => $variant) {
                ShopifyVariant::query()->updateOrCreate(
                    ['shopify_id' => $variant['id']],
                    [
                        'product_id' => $product->id,
                        'sku' => $variant['sku'] ?? '',
                        'option_values' => array_map(fn (array $option): string => $option['value'], $variant['selectedOptions'] ?? []),
                        'position' => $index,
                    ],
                );
            }
        }

        foreach ($data['collections']['nodes'] ?? [] as $node) {
            $collection = ShopifyCollection::query()->updateOrCreate(
                ['shopify_id' => $node['id']],
                [
                    'handle' => $node['handle'],
                    'title' => $node['title'],
                ],
            );

            $ids = ShopifyProduct::query()
                ->whereIn('shopify_id', array_column($node['products']['nodes'] ?? [], 'id'))
                ->pluck('id');

            $collection->products()->sync($ids);
        }
    }
}
