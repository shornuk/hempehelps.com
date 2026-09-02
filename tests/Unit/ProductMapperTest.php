<?php

namespace Tests\Unit;

use App\Shopify\Mappers\ProductMapper;
use PHPUnit\Framework\TestCase;

class ProductMapperTest extends TestCase
{
    public function test_maps_storefront_product_payload(): void
    {
        $product = (new ProductMapper)->map([
            'id' => 'gid://shopify/Product/1',
            'handle' => 'cbd-hot-muscle-joint-gel',
            'title' => 'Hot Gel',
            'descriptionHtml' => '<p>Warm.</p>',
            'availableForSale' => true,
            'vendor' => 'HEMPE',
            'productType' => 'Gel',
            'tags' => ['hemp'],
            'featuredImage' => ['url' => 'https://cdn.shopify.com/gel.jpg', 'width' => 800, 'height' => 800, 'altText' => 'Gel'],
            'images' => ['nodes' => [['url' => 'https://cdn.shopify.com/gel.jpg', 'width' => 800, 'height' => 800, 'altText' => 'Gel']]],
            'variants' => ['nodes' => [[
                'id' => 'gid://shopify/ProductVariant/1',
                'title' => 'Default',
                'sku' => 'HOT',
                'availableForSale' => true,
                'quantityAvailable' => 12,
                'price' => ['amount' => '24.00', 'currencyCode' => 'GBP'],
                'compareAtPrice' => null,
                'selectedOptions' => [['name' => 'Title', 'value' => 'Default']],
            ]]],
        ]);

        $this->assertSame('cbd-hot-muscle-joint-gel', $product->handle);
        $this->assertSame('GBP 24.00', $product->selectedVariant->price->formatted());
        $this->assertTrue($product->available);
        $this->assertStringContainsString('width=400', $product->featuredImage->transformed(400));
    }
}
