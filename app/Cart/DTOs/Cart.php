<?php

namespace App\Cart\DTOs;

use App\Catalog\DTOs\Money;

final readonly class Cart
{
    /**
     * @param  list<CartLine>  $lines
     * @param  list<string>  $discountCodes
     */
    public function __construct(
        public int $totalQuantity,
        public Money $subtotal,
        public Money $total,
        public array $lines,
        public array $discountCodes = [],
    ) {}

    public function isEmpty(): bool
    {
        return $this->totalQuantity === 0;
    }
}
