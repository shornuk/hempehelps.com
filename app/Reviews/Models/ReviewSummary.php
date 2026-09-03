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
        $query = Review::query()
            ->where('shopify_product_id', $productId)
            ->where('status', ReviewStatus::Published);

        static::query()->updateOrCreate(
            ['shopify_product_id' => $productId],
            [
                'average_rating' => round((float) $query->avg('rating'), 2),
                'review_count' => $query->count(),
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
