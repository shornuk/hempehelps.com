<?php

namespace App\Shopify;

use App\Markets\MarketContext;
use App\Shopify\Exceptions\MissingCredentialsException;
use App\Shopify\Exceptions\ShopifyException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class StorefrontClient
{
    public function __construct(
        private readonly MarketContext $market,
    ) {}

    /**
     * @param  array<string, mixed>  $variables
     * @return array<string, mixed>
     */
    public function query(string $document, array $variables = []): array
    {
        $response = $this->request()->post($this->endpoint(), [
            'query' => $this->withContext($document),
            'variables' => $variables,
        ]);

        if ($response->failed()) {
            throw new ShopifyException('Storefront API request failed: '.$response->status());
        }

        /** @var array{data?: array<string, mixed>, errors?: list<array{message?: string}>} $payload */
        $payload = $response->json();

        if (! empty($payload['errors'])) {
            $message = $payload['errors'][0]['message'] ?? 'Unknown Storefront API error';

            throw new ShopifyException($message);
        }

        return $payload['data'] ?? [];
    }

    public function document(string $name): string
    {
        $path = base_path('graphql/storefront/'.$name.'.graphql');

        if (! is_file($path)) {
            throw new ShopifyException('Missing Storefront GraphQL document: '.$name);
        }

        return (string) file_get_contents($path);
    }

    protected function request(): PendingRequest
    {
        $token = config('shopify.storefront_token');

        if (! is_string($token) || $token === '') {
            throw new MissingCredentialsException('SHOPIFY_STOREFRONT_PRIVATE_TOKEN is not configured.');
        }

        return Http::baseUrl('https://'.config('shopify.shop'))
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Shopify-Storefront-Private-Token' => $token,
                'Shopify-Storefront-Buyer-IP' => request()->ip() ?? '127.0.0.1',
            ]);
    }

    protected function endpoint(): string
    {
        return '/api/'.config('shopify.api_version').'/graphql.json';
    }

    protected function withContext(string $document): string
    {
        if (str_contains($document, '@inContext')) {
            return $document;
        }

        $country = $this->market->shopifyCountry;
        $language = strtoupper($this->market->language);

        return preg_replace(
            '/^(query|mutation)\s+(\w+)/m',
            '$1 $2 @inContext(country: '.$country.', language: '.$language.')',
            $document,
            1,
        ) ?? $document;
    }
}
