<?php

namespace App\Catalog;

use App\Catalog\DTOs\Collection;
use App\Catalog\DTOs\Image;
use App\Catalog\DTOs\Money;
use App\Catalog\DTOs\Product;
use App\Catalog\DTOs\Variant;
use App\Catalog\Models\ShopifyCollection;
use App\Catalog\Models\ShopifyProduct;
use App\Catalog\Models\ShopifyVariant;
use App\Markets\MarketContext;
use App\Shopify\Exceptions\MissingCredentialsException;
use App\Shopify\Mappers\ProductMapper;
use App\Shopify\StorefrontClient;
use Illuminate\Support\Facades\Cache;

class ProductRepository
{
    public function __construct(
        private readonly StorefrontClient $storefront,
        private readonly ProductMapper $mapper,
        private readonly MarketContext $market,
    ) {}

    public function getProductByHandle(string $handle): ?Product
    {
        $local = ShopifyProduct::query()->where('handle', $handle)->first();

        $cacheKey = 'product:'.$handle.':'.$this->market->key;

        try {
            return Cache::remember($cacheKey, config('shopify.cache_ttl.product'), function () use ($handle): ?Product {
                $data = $this->storefront->query($this->storefront->document('product-by-handle'), [
                    'handle' => $handle,
                ]);

                if (empty($data['product'])) {
                    return null;
                }

                return $this->mapper->map($data['product'], $this->market->currency);
            });
        } catch (MissingCredentialsException) {
            return $local ? $this->fromLocal($local) : null;
        }
    }

    /**
     * @return list<Product>
     */
    public function getProducts(): array
    {
        return array_values(ShopifyProduct::query()
            ->where('status', 'active')
            ->orderBy('title')
            ->get()
            ->map(fn (ShopifyProduct $product): Product => $this->getProductByHandle($product->handle) ?? $this->fromLocal($product))
            ->filter()
            ->values()
            ->all());
    }

    public function getCollectionByHandle(string $handle): ?Collection
    {
        $local = ShopifyCollection::query()->where('handle', $handle)->first();

        if (! $local) {
            return null;
        }

        $products = array_values($local->products
            ->map(fn (ShopifyProduct $product): Product => $this->getProductByHandle($product->handle) ?? $this->fromLocal($product))
            ->all());

        return new Collection(
            id: $local->shopify_id,
            handle: $local->handle,
            title: $local->title,
            descriptionHtml: '',
            products: $products,
        );
    }

    public function fromLocal(ShopifyProduct $product): Product
    {
        $mappedVariant = $this->mapLocalVariant($product->variants->first());

        $image = $product->featured_image_url
            ? new Image($product->featured_image_url, alt: $product->title)
            : null;

        return new Product(
            id: $product->shopify_id,
            handle: $product->handle,
            title: $product->title,
            descriptionHtml: '',
            available: $product->status === 'active',
            selectedVariant: $mappedVariant,
            variants: [$mappedVariant],
            images: $image instanceof Image ? [$image] : [],
            tags: $product->tags,
            featuredImage: $image,
            vendor: $product->vendor ?? '',
            productType: $product->product_type ?? '',
        );
    }

    private function mapLocalVariant(?ShopifyVariant $variant): Variant
    {
        if ($variant === null) {
            return new Variant(
                id: '',
                title: 'Default',
                sku: '',
                price: new Money('0.00', $this->market->currency),
                compareAtPrice: null,
                available: true,
                quantityAvailable: null,
                options: [],
            );
        }

        return new Variant(
            id: $variant->shopify_id,
            title: 'Default',
            sku: (string) $variant->sku,
            price: new Money('0.00', $this->market->currency),
            compareAtPrice: null,
            available: true,
            quantityAvailable: null,
            options: $variant->option_values,
        );
    }
}
