<?php

namespace App\Catalog\Models;

use App\Reviews\Models\Review;
use App\Reviews\Models\ReviewSummary;
use Database\Factories\ShopifyProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property list<string> $tags
 */
#[Fillable([
    'shopify_id',
    'handle',
    'title',
    'status',
    'product_type',
    'vendor',
    'tags',
    'featured_image_url',
    'published_at',
    'synced_at',
])]
class ShopifyProduct extends Model
{
    /** @use HasFactory<ShopifyProductFactory> */
    use HasFactory;

    protected static function newFactory(): ShopifyProductFactory
    {
        return ShopifyProductFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<ShopifyVariant, $this>
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ShopifyVariant::class, 'product_id');
    }

    /**
     * @return HasOne<ProductContent, $this>
     */
    public function content(): HasOne
    {
        return $this->hasOne(ProductContent::class, 'shopify_product_id');
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'shopify_product_id');
    }

    /**
     * @return HasOne<ReviewSummary, $this>
     */
    public function reviewSummary(): HasOne
    {
        return $this->hasOne(ReviewSummary::class, 'shopify_product_id');
    }

    /**
     * @return BelongsToMany<ShopifyCollection, $this>
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(ShopifyCollection::class, 'collection_product', 'product_id', 'collection_id');
    }
}
