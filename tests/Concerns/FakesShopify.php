<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Http;

trait FakesShopify
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function fakeStorefront(array $data = []): void
    {
        config([
            'shopify.shop' => 'hempehelps.myshopify.com',
            'shopify.storefront_token' => 'sf-test-token',
            'shopify.admin_token' => 'admin-test-token',
            'shopify.webhook_secret' => 'whsec-test',
        ]);

        Http::fake([
            'https://hempehelps.myshopify.com/api/*' => Http::response(['data' => $data], 200),
            'https://hempehelps.myshopify.com/admin/api/*' => Http::response(['data' => $data], 200),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function cartPayload(string $id = 'gid://shopify/Cart/1?key=secret-cart-key'): array
    {
        return [
            'id' => $id,
            'totalQuantity' => 1,
            'checkoutUrl' => 'https://hempehelps.myshopify.com/cart/c/abc',
            'discountCodes' => [],
            'cost' => [
                'subtotalAmount' => ['amount' => '24.00', 'currencyCode' => 'GBP'],
                'totalAmount' => ['amount' => '24.00', 'currencyCode' => 'GBP'],
            ],
            'lines' => [
                'nodes' => [[
                    'id' => 'gid://shopify/CartLine/1',
                    'quantity' => 1,
                    'cost' => ['totalAmount' => ['amount' => '24.00', 'currencyCode' => 'GBP']],
                    'merchandise' => [
                        'id' => 'gid://shopify/ProductVariant/1',
                        'title' => 'Default',
                        'image' => null,
                        'product' => ['title' => 'Hot Gel', 'handle' => 'cbd-hot-muscle-joint-gel'],
                    ],
                ]],
            ],
        ];
    }
}
