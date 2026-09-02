<?php

namespace Database\Seeders;

use App\Content\Models\Page;
use App\Markets\Models\Market;
use App\Seo\Models\Redirect;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    public function run(): void
    {
        $markets = [
            ['key' => 'gb', 'country_codes' => ['GB'], 'locale' => 'en-GB', 'currency' => 'GBP', 'host' => 'hempehelps.com', 'path_prefix' => null, 'shopify_country' => 'GB', 'is_default' => true],
            ['key' => 'us', 'country_codes' => ['US'], 'locale' => 'en-US', 'currency' => 'USD', 'host' => 'hempehelps.com', 'path_prefix' => 'en-us', 'shopify_country' => 'US', 'is_default' => false],
            ['key' => 'eu', 'country_codes' => ['DE', 'FR', 'IE', 'NL', 'ES', 'IT'], 'locale' => 'en-GB', 'currency' => 'EUR', 'host' => 'hempehelps.com', 'path_prefix' => 'eu', 'shopify_country' => 'DE', 'is_default' => false],
            ['key' => 'international', 'country_codes' => ['AU', 'NZ'], 'locale' => 'en-GB', 'currency' => 'GBP', 'host' => 'hempehelps.com', 'path_prefix' => 'international', 'shopify_country' => 'GB', 'is_default' => false],
        ];

        foreach ($markets as $market) {
            Market::query()->updateOrCreate(['key' => $market['key']], $market);
        }

        Page::query()->updateOrCreate(
            ['slug' => 'our-story', 'market_id' => null],
            ['title' => 'Our story', 'body' => '<p>HEMPE makes plant-powered recovery products.</p>', 'published_at' => now()],
        );
        Page::query()->updateOrCreate(
            ['slug' => 'contact', 'market_id' => null],
            ['title' => 'Contact', 'body' => '<p>Email hello@hempehelps.com</p>', 'published_at' => now()],
        );

        foreach ([
            ['from_path' => '/collections/all', 'to_url' => '/shop'],
            ['from_path' => '/collections/default', 'to_url' => '/shop'],
            ['from_path' => '/account/login', 'to_url' => '/login'],
            ['from_path' => '/account/register', 'to_url' => '/login'],
            ['from_path' => '/blogs/guides', 'to_url' => '/guides'],
        ] as $redirect) {
            Redirect::query()->updateOrCreate(
                ['from_path' => $redirect['from_path'], 'market_id' => null],
                ['to_url' => $redirect['to_url'], 'status' => 301],
            );
        }
    }
}
