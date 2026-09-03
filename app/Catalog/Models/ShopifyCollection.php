<?php

namespace App\Catalog\Models;

use Database\Factories\ShopifyCollectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['shopify_id', 'handle', 'title'])]
class ShopifyCollection extends Model
{
    /** @use HasFactory<ShopifyCollectionFactory> */
    use HasFactory;

    protected static function newFactory(): ShopifyCollectionFactory
    {
        return ShopifyCollectionFactory::new();
    }

    /**
     * @return BelongsToMany<ShopifyProduct, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(ShopifyProduct::class, 'collection_product', 'collection_id', 'product_id');
    }
}
