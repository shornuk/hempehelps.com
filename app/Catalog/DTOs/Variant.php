<?php

namespace App\Catalog\DTOs;

final readonly class Variant
{
    /**
     * @param  array<int, string>  $options
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $sku,
        public Money $price,
        public ?Money $compareAtPrice,
        public bool $available,
        public ?int $quantityAvailable,
        public array $options = [],
    ) {}
}
