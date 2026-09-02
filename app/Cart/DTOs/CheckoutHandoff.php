<?php

namespace App\Cart\DTOs;

final readonly class CheckoutHandoff
{
    public function __construct(
        public string $url,
    ) {}
}
