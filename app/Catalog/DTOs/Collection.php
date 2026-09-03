<?php

namespace App\Catalog\DTOs;

final readonly class Collection
{
    /**
     * @param  list<Product>  $products
     */
    public function __construct(
        public string $id,
        public string $handle,
        public string $title,
        public string $descriptionHtml,
        public array $products,
    ) {}
}
