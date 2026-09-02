<?php

namespace App\Catalog\DTOs;

final readonly class Product
{
    /**
     * @param  list<Variant>  $variants
     * @param  list<Image>  $images
     * @param  list<string>  $tags
     */
    public function __construct(
        public string $id,
        public string $handle,
        public string $title,
        public string $descriptionHtml,
        public bool $available,
        public Variant $selectedVariant,
        public array $variants,
        public array $images,
        public array $tags,
        public ?Image $featuredImage = null,
        public string $vendor = '',
        public string $productType = '',
    ) {}
}
