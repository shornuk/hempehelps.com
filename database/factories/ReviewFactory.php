<?php

namespace Database\Factories;

use App\Catalog\Models\ShopifyProduct;
use App\Enums\ReviewSource;
use App\Enums\ReviewStatus;
use App\Reviews\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'shopify_product_id' => ShopifyProduct::factory(),
            'author_name' => fake()->name(),
            'author_email' => fake()->safeEmail(),
            'rating' => 5,
            'title' => fake()->sentence(3),
            'body' => fake()->paragraph(),
            'source' => ReviewSource::Admin,
            'verified_purchase' => false,
            'status' => ReviewStatus::Published,
            'published_at' => now(),
        ];
    }
}
