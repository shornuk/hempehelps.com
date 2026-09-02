<?php

namespace App\Markets;

use App\Markets\Models\Market;
use Illuminate\Http\Request;

class GeoSuggester
{
    public function detectedCountry(Request $request): ?string
    {
        $override = $request->header('X-Test-Country') ?: env('GEOIP_COUNTRY');

        if (is_string($override) && $override !== '') {
            return strtoupper($override);
        }

        $cf = $request->header('CF-IPCountry');

        if (is_string($cf) && $cf !== '' && $cf !== 'XX') {
            return strtoupper($cf);
        }

        return null;
    }

    public function suggestedMarket(Request $request, MarketContext $current): ?Market
    {
        if ($request->cookie(config('markets.cookie'))) {
            return null;
        }

        if ($request->session()->get('market_suggestion_dismissed')) {
            return null;
        }

        $country = $this->detectedCountry($request);

        if ($country === null || $country === $current->countryCode) {
            return null;
        }

        return Market::query()
            ->whereJsonContains('country_codes', $country)
            ->first();
    }
}
