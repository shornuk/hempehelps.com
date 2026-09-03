<?php

namespace App\Markets\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $key
 * @property array<int, string> $country_codes
 * @property string $locale
 * @property string $currency
 * @property string $host
 * @property string|null $path_prefix
 * @property string $shopify_country
 * @property bool $is_default
 */
#[Fillable([
    'key',
    'country_codes',
    'locale',
    'currency',
    'host',
    'path_prefix',
    'shopify_country',
    'is_default',
])]
class Market extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'country_codes' => 'array',
            'is_default' => 'boolean',
        ];
    }
}
