<?php

namespace Database\Factories;

use App\Catalog\Models\ShopifyProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ShopifyProduct>
 */
class ShopifyProductFactory extends Factory
{
    protected $model = ShopifyProduct::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'shopify_id' => 'gid://shopify/Product/'.fake()->unique()->numerify('########'),
            'handle' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'title' => $title,
            'status' => 'active',
            'product_type' => 'Gel',
            'vendor' => 'HEMPE',
            'tags' => ['hemp'],
            'featured_image_url' => null,
            'published_at' => now(),
            'synced_at' => now(),
        ];
    }
}
