<?php

namespace App\Reviews\Models;

use App\Catalog\Models\ShopifyProduct;
use App\Catalog\Models\ShopifyVariant;
use App\Enums\ReviewSource;
use App\Enums\ReviewStatus;
use App\Markets\Models\Market;
use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'shopify_product_id',
    'shopify_variant_id',
    'author_name',
    'author_email',
    'rating',
    'title',
    'body',
    'photos',
    'source',
    'external_id',
    'verified_purchase',
    'status',
    'published_at',
    'market_id',
])]
class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory;

    protected static function newFactory(): ReviewFactory
    {
        return ReviewFactory::new();
    }

    protected static function booted(): void
    {
        static::saved(fn (self $review) => ReviewSummary::refreshFor($review->shopify_product_id));
        static::deleted(fn (self $review) => ReviewSummary::refreshFor($review->shopify_product_id));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'photos' => 'array',
            'verified_purchase' => 'boolean',
            'published_at' => 'datetime',
            'source' => ReviewSource::class,
            'status' => ReviewStatus::class,
        ];
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Published);
    }

    /**
     * @return BelongsTo<ShopifyProduct, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopifyProduct::class, 'shopify_product_id');
    }

    /**
     * @return BelongsTo<ShopifyVariant, $this>
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ShopifyVariant::class, 'shopify_variant_id');
    }

    /**
     * @return BelongsTo<Market, $this>
     */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }
}
