<?php

namespace App\Catalog\Models;

use App\Markets\Models\Market;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'shopify_product_id',
    'market_id',
    'blocks',
    'meta_title',
    'meta_description',
])]
class ProductContent extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blocks' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ShopifyProduct, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopifyProduct::class, 'shopify_product_id');
    }

    /**
     * @return BelongsTo<Market, $this>
     */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }
}
