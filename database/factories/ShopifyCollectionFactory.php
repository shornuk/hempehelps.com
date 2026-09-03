<?php

namespace Database\Factories;

use App\Catalog\Models\ShopifyCollection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ShopifyCollection>
 */
class ShopifyCollectionFactory extends Factory
{
    protected $model = ShopifyCollection::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(2);

        return [
            'shopify_id' => 'gid://shopify/Collection/'.fake()->unique()->numerify('########'),
            'handle' => Str::slug($title),
            'title' => $title,
        ];
    }
}
