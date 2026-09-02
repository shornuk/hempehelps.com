<?php

namespace App\Cart\DTOs;

use App\Catalog\DTOs\Image;
use App\Catalog\DTOs\Money;

final readonly class CartLine
{
    public function __construct(
        public string $id,
        public string $merchandiseId,
        public string $title,
        public int $quantity,
        public Money $amount,
        public ?Image $image = null,
        public string $productHandle = '',
    ) {}
}
