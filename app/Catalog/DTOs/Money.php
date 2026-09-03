<?php

namespace App\Catalog\DTOs;

final readonly class Money
{
    public function __construct(
        public string $amount,
        public string $currencyCode,
    ) {}

    public function formatted(): string
    {
        return $this->currencyCode.' '.$this->amount;
    }

    /**
     * @param  array{amount?: string, currencyCode?: string}|null  $payload
     */
    public static function fromShopify(?array $payload, string $fallbackCurrency = 'GBP'): self
    {
        return new self(
            amount: (string) ($payload['amount'] ?? '0.00'),
            currencyCode: (string) ($payload['currencyCode'] ?? $fallbackCurrency),
        );
    }
}
