<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShopifyPocStatusCommand extends Command
{
    protected $signature = 'shopify:poc-status';

    protected $description = 'Print Phase 0 spike checklist for the Headless storefront';

    public function handle(): int
    {
        $this->info('HEMPEhelps Phase 0 — re-run against hempehelps.myshopify.com before launch.');
        $this->newLine();

        $rows = [
            ['Markets currency', 'PDP @inContext vs cart countryCode vs checkout presentment. If GBP-locked, show “Charged in GBP”.'],
            ['Collabs', '/discount/{code} + redirects + analytics. Official docs are Hydrogen-only.'],
            ['GA4 purchase', 'Web pixel checkout_completed only. Measure real checkoutUrl host. Decorate _gl.'],
            ['Cart GID', 'Encrypted session only. Never echo gid://shopify/Cart in HTML.'],
            ['Admin PCD', 'OTP then Admin orders. Requires Protected Customer Data approval.'],
            ['Headless channel', 'Publish products to Headless. Theme token is not enough.'],
            ['Shop subdomain', 'Default hempehelps.myshopify.com (not hempe-helps). Confirm before creating apps.'],
            ['US fulfilment', 'Headless-channel test order still routes to the US fulfilment app.'],
        ];

        $this->table(['PoC', 'What to confirm'], $rows);
        $this->comment('See docs/spikes/PHASE-0.md');

        $this->newLine();
        $this->line('Shop: '.config('shopify.shop'));
        $this->line('API: '.config('shopify.api_version'));
        $this->line('Storefront token: '.(config('shopify.storefront_token') ? 'set' : 'missing'));
        $this->line('Admin token: '.(config('shopify.admin_token') ? 'set' : 'missing'));
        $this->line('Webhook secret: '.(config('shopify.webhook_secret') ? 'set' : 'missing'));

        return self::SUCCESS;
    }
}
