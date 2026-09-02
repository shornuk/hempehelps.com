<?php

namespace App\Reviews\Models;

use App\Catalog\Models\ShopifyProduct;
use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['shopify_product_id', 'average_rating', 'review_count'])]
class ReviewSummary extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'average_rating' => 'float',
        ];
    }

    public static function refreshFor(int $productId): void
    {
        $stats = Review::query()
            ->where('shopify_product_id', $productId)
            ->where('status', ReviewStatus::Published)
            ->selectRaw('AVG(rating) as average_rating, COUNT(*) as review_count')
            ->first();

        static::query()->updateOrCreate(
            ['shopify_product_id' => $productId],
            [
                'average_rating' => round((float) ($stats?->average_rating ?? 0), 2),
                'review_count' => (int) ($stats?->review_count ?? 0),
            ],
        );
    }

    /**
     * @return BelongsTo<ShopifyProduct, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopifyProduct::class, 'shopify_product_id');
    }
}
