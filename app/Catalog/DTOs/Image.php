<?php

namespace App\Catalog\DTOs;

final readonly class Image
{
    public function __construct(
        public string $url,
        public ?int $width = null,
        public ?int $height = null,
        public string $alt = '',
    ) {}

    public function transformed(int $width): string
    {
        if (! str_contains($this->url, 'cdn.shopify.com') && ! str_contains($this->url, 'shopifycdn.com')) {
            return $this->url;
        }

        $separator = str_contains($this->url, '?') ? '&' : '?';

        return $this->url.$separator.'width='.$width;
    }

    /**
     * @param  array{url?: string, width?: int, height?: int, altText?: string}|null  $payload
     */
    public static function fromShopify(?array $payload): ?self
    {
        if ($payload === null || empty($payload['url'])) {
            return null;
        }

        return new self(
            url: (string) $payload['url'],
            width: isset($payload['width']) ? (int) $payload['width'] : null,
            height: isset($payload['height']) ? (int) $payload['height'] : null,
            alt: (string) ($payload['altText'] ?? ''),
        );
    }
}
