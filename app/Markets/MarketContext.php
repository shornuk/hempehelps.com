<?php

namespace App\Markets;

final readonly class MarketContext
{
    public function __construct(
        public string $key,
        public string $countryCode,
        public string $shopifyCountry,
        public string $locale,
        public string $language,
        public string $currency,
        public string $host,
        public ?string $pathPrefix,
        public bool $isDefault,
        public bool $chargeInGbp = true,
    ) {}

    public function prefix(): string
    {
        return $this->pathPrefix ? '/'.$this->pathPrefix : '';
    }

    public function url(string $path = '/'): string
    {
        $path = '/'.ltrim($path, '/');

        if ($this->prefix() === '') {
            return $path;
        }

        return rtrim($this->prefix().$path, '/') ?: '/';
    }

    public static function default(): self
    {
        return new self(
            key: 'gb',
            countryCode: 'GB',
            shopifyCountry: 'GB',
            locale: 'en-GB',
            language: 'EN',
            currency: 'GBP',
            host: 'hempehelps.com',
            pathPrefix: null,
            isDefault: true,
        );
    }
}
