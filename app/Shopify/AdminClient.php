<?php

namespace App\Shopify;

use App\Shopify\Exceptions\MissingCredentialsException;
use App\Shopify\Exceptions\ShopifyException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AdminClient
{
    /**
     * @param  array<string, mixed>  $variables
     * @return array<string, mixed>
     */
    public function query(string $document, array $variables = []): array
    {
        $response = $this->request()->post($this->endpoint(), [
            'query' => $document,
            'variables' => $variables,
        ]);

        if ($response->failed()) {
            throw new ShopifyException('Admin API request failed: '.$response->status());
        }

        /** @var array{data?: array<string, mixed>, errors?: list<array{message?: string}>} $payload */
        $payload = $response->json();

        if (! empty($payload['errors'])) {
            throw new ShopifyException($payload['errors'][0]['message'] ?? 'Unknown Admin API error');
        }

        return $payload['data'] ?? [];
    }

    public function document(string $name): string
    {
        $path = base_path('graphql/admin/'.$name.'.graphql');

        if (! is_file($path)) {
            throw new ShopifyException('Missing Admin GraphQL document: '.$name);
        }

        return (string) file_get_contents($path);
    }

    protected function request(): PendingRequest
    {
        $token = config('shopify.admin_token');

        if (! is_string($token) || $token === '') {
            throw new MissingCredentialsException('SHOPIFY_ADMIN_ACCESS_TOKEN is not configured.');
        }

        return Http::baseUrl('https://'.config('shopify.shop'))
            ->acceptJson()
            ->asJson()
            ->withToken($token)
            ->withHeaders(['X-Shopify-Access-Token' => $token]);
    }

    protected function endpoint(): string
    {
        return '/admin/api/'.config('shopify.api_version').'/graphql.json';
    }
}
