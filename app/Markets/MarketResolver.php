<?php

namespace App\Markets;

use App\Markets\Models\Market;
use Illuminate\Http\Request;

class MarketResolver
{
    public function resolve(Request $request): MarketContext
    {
        $host = strtolower($request->getHost());
        $first = $request->segment(1);
        $prefixes = config('markets.prefixes', []);

        if (is_string($first) && isset($prefixes[$first])) {
            return $this->contextForKey((string) $prefixes[$first], $host, $first);
        }

        $hosts = config('markets.hosts', []);

        if (isset($hosts[$host])) {
            return $this->contextForKey((string) $hosts[$host], $host);
        }

        return $this->contextForKey((string) config('markets.default', 'gb'), $host);
    }

    public function contextForKey(string $key, string $host = 'localhost', ?string $prefix = null): MarketContext
    {
        $market = Market::query()->where('key', $key)->first();

        if (! $market) {
            $default = MarketContext::default();

            return new MarketContext(
                key: $default->key,
                countryCode: $default->countryCode,
                shopifyCountry: $default->shopifyCountry,
                locale: $default->locale,
                language: $default->language,
                currency: $default->currency,
                host: $host,
                pathPrefix: $prefix,
                isDefault: true,
            );
        }

        $countries = $market->country_codes;

        return new MarketContext(
            key: $market->key,
            countryCode: $countries[0] ?? 'GB',
            shopifyCountry: $market->shopify_country,
            locale: $market->locale,
            language: strtoupper(substr($market->locale, 0, 2)),
            currency: $market->currency,
            host: $market->host ?: $host,
            pathPrefix: $prefix ?? $market->path_prefix,
            isDefault: $market->is_default,
        );
    }
}
