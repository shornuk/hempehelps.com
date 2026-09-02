<?php

namespace App\Shopify\Mappers;

use App\Cart\DTOs\Cart;
use App\Cart\DTOs\CartLine;
use App\Catalog\DTOs\Image;
use App\Catalog\DTOs\Money;

class CartMapper
{
    /**
     * @param  array<string, mixed>  $node
     */
    public function map(array $node, string $fallbackCurrency = 'GBP'): Cart
    {
        $lines = [];

        foreach ($node['lines']['nodes'] ?? [] as $line) {
            $merchandise = $line['merchandise'] ?? [];
            $product = $merchandise['product'] ?? [];

            $lines[] = new CartLine(
                id: (string) $line['id'],
                merchandiseId: (string) ($merchandise['id'] ?? ''),
                title: (string) ($product['title'] ?? $merchandise['title'] ?? 'Item'),
                quantity: (int) ($line['quantity'] ?? 0),
                amount: Money::fromShopify($line['cost']['totalAmount'] ?? null, $fallbackCurrency),
                image: Image::fromShopify($merchandise['image'] ?? $product['featuredImage'] ?? null),
                productHandle: (string) ($product['handle'] ?? ''),
            );
        }

        $codes = [];

        foreach ($node['discountCodes'] ?? [] as $code) {
            if (! empty($code['applicable']) && ! empty($code['code'])) {
                $codes[] = (string) $code['code'];
            }
        }

        return new Cart(
            totalQuantity: (int) ($node['totalQuantity'] ?? 0),
            subtotal: Money::fromShopify($node['cost']['subtotalAmount'] ?? null, $fallbackCurrency),
            total: Money::fromShopify($node['cost']['totalAmount'] ?? null, $fallbackCurrency),
            lines: $lines,
            discountCodes: $codes,
        );
    }
}
