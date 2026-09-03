<?php

namespace App\Seo\Jobs;

use App\Seo\Models\Redirect;
use App\Shopify\AdminClient;
use App\Shopify\Exceptions\MissingCredentialsException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncShopifyRedirectsJob implements ShouldQueue
{
    use Queueable;

    public function handle(AdminClient $admin): void
    {
        try {
            $data = $admin->query($admin->document('url-redirects'));
        } catch (MissingCredentialsException $exception) {
            Log::warning($exception->getMessage());

            return;
        }

        foreach ($data['urlRedirects']['nodes'] ?? [] as $node) {
            Redirect::query()->updateOrCreate(
                ['from_path' => $node['path'], 'market_id' => null],
                ['to_url' => $node['target'], 'status' => 301],
            );
        }
    }
}
