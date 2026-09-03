<?php

namespace App\Catalog\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property list<string> $option_values
 */
#[Fillable([
    'shopify_id',
    'product_id',
    'sku',
    'option_values',
    'position',
])]
class ShopifyVariant extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'option_values' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ShopifyProduct, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopifyProduct::class, 'product_id');
    }
}
