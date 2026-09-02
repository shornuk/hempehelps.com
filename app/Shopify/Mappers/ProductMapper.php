<?php

namespace App\Shopify\Mappers;

use App\Catalog\DTOs\Image;
use App\Catalog\DTOs\Money;
use App\Catalog\DTOs\Product;
use App\Catalog\DTOs\Variant;

class ProductMapper
{
    /**
     * @param  array<string, mixed>  $node
     */
    public function map(array $node, string $fallbackCurrency = 'GBP'): Product
    {
        $variants = [];

        foreach ($node['variants']['nodes'] ?? [] as $variantNode) {
            $variants[] = new Variant(
                id: (string) $variantNode['id'],
                title: (string) ($variantNode['title'] ?? 'Default'),
                sku: (string) ($variantNode['sku'] ?? ''),
                price: Money::fromShopify($variantNode['price'] ?? null, $fallbackCurrency),
                compareAtPrice: isset($variantNode['compareAtPrice'])
                    ? Money::fromShopify($variantNode['compareAtPrice'], $fallbackCurrency)
                    : null,
                available: (bool) ($variantNode['availableForSale'] ?? false),
                quantityAvailable: isset($variantNode['quantityAvailable'])
                    ? (int) $variantNode['quantityAvailable']
                    : null,
                options: array_map(
                    fn (array $option): string => (string) $option['value'],
                    $variantNode['selectedOptions'] ?? [],
                ),
            );
        }

        $images = [];

        foreach ($node['images']['nodes'] ?? [] as $imageNode) {
            $image = Image::fromShopify($imageNode);

            if ($image instanceof Image) {
                $images[] = $image;
            }
        }

        $selected = $variants[0] ?? new Variant(
            id: '',
            title: 'Default',
            sku: '',
            price: new Money('0.00', $fallbackCurrency),
            compareAtPrice: null,
            available: false,
            quantityAvailable: null,
        );

        return new Product(
            id: (string) $node['id'],
            handle: (string) $node['handle'],
            title: (string) $node['title'],
            descriptionHtml: (string) ($node['descriptionHtml'] ?? ''),
            available: (bool) ($node['availableForSale'] ?? false),
            selectedVariant: $selected,
            variants: $variants,
            images: $images,
            tags: array_map(strval(...), $node['tags'] ?? []),
            featuredImage: Image::fromShopify($node['featuredImage'] ?? null),
            vendor: (string) ($node['vendor'] ?? ''),
            productType: (string) ($node['productType'] ?? ''),
        );
    }
}
