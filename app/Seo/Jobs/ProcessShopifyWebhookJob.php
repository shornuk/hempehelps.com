<?php

namespace App\Seo\Jobs;

use App\Catalog\Jobs\SyncProductJob;
use App\Catalog\Models\ShopifyProduct;
use App\Seo\WebhookEvent;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessShopifyWebhookJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public int $eventId,
        public string $topic,
        public array $payload,
    ) {}

    public function uniqueId(): string
    {
        return (string) $this->eventId;
    }

    public function handle(): void
    {
        match ($this->topic) {
            'products/create', 'products/update' => SyncProductJob::dispatchSync($this->payload),
            'products/delete' => $this->deleteProduct(),
            default => null,
        };

        WebhookEvent::query()->whereKey($this->eventId)->update(['processed_at' => now()]);
    }

    protected function deleteProduct(): void
    {
        $id = isset($this->payload['admin_graphql_api_id'])
            ? (string) $this->payload['admin_graphql_api_id']
            : 'gid://shopify/Product/'.$this->payload['id'];

        ShopifyProduct::query()->where('shopify_id', $id)->delete();
    }
}
