<?php

namespace App\Reviews\Jobs;

use App\Catalog\Models\ShopifyProduct;
use App\Enums\ReviewSource;
use App\Enums\ReviewStatus;
use App\Reviews\Models\Review;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ImportReviewsJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  list<array<string, mixed>>|null  $rows
     */
    public function __construct(
        public ?string $path = null,
        public ?array $rows = null,
    ) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function handle(): array
    {
        $unassigned = [];

        foreach ($this->rows() as $row) {
            $product = $this->matchProduct($row);

            if (! $product) {
                $unassigned[] = $row;

                continue;
            }

            Review::query()->updateOrCreate(
                ['external_id' => (string) ($row['id'] ?? $row['external_id'] ?? uniqid('import-', true))],
                [
                    'shopify_product_id' => $product->id,
                    'author_name' => $row['reviewer_name'] ?? $row['name'] ?? 'Customer',
                    'author_email' => $row['reviewer_email'] ?? $row['email'] ?? null,
                    'rating' => (int) ($row['rating'] ?? 5),
                    'title' => $row['title'] ?? null,
                    'body' => $row['body'] ?? $row['content'] ?? '',
                    'photos' => $this->photos($row),
                    'source' => ReviewSource::Import,
                    'verified_purchase' => (bool) ($row['verified'] ?? $row['curated'] ?? false),
                    'status' => ReviewStatus::Published,
                    'published_at' => now(),
                ],
            );
        }

        return $unassigned;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function rows(): array
    {
        if (is_array($this->rows)) {
            return $this->rows;
        }

        if ($this->path === null) {
            return [];
        }

        $contents = Storage::get($this->path);

        if (! is_string($contents) || $contents === '') {
            return [];
        }

        $decoded = json_decode($contents, true);

        if (is_array($decoded)) {
            return $decoded['reviews'] ?? $decoded;
        }

        return $this->fromCsv($contents);
    }

    /**
     * @return list<array<string, string>>
     */
    protected function fromCsv(string $contents): array
    {
        $lines = preg_split('/\r\n|\n|\r/', trim($contents)) ?: [];
        $header = str_getcsv(array_shift($lines) ?? '');
        $rows = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $rows[] = array_combine($header, str_getcsv($line));
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    protected function matchProduct(array $row): ?ShopifyProduct
    {
        $handle = $row['product_handle'] ?? $row['handle'] ?? null;
        $external = $row['product_external_id'] ?? $row['product_id'] ?? null;

        if (is_string($handle) && $handle !== '') {
            $byHandle = ShopifyProduct::query()->where('handle', $handle)->first();

            if ($byHandle) {
                return $byHandle;
            }
        }

        if ($external) {
            $gid = str_starts_with((string) $external, 'gid://')
                ? (string) $external
                : 'gid://shopify/Product/'.$external;

            return ShopifyProduct::query()->where('shopify_id', $gid)->first();
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return list<string>
     */
    protected function photos(array $row): array
    {
        if (isset($row['picture_urls']) && is_array($row['picture_urls'])) {
            return array_values($row['picture_urls']);
        }

        if (! empty($row['pictures'])) {
            return array_values(array_filter(explode(',', (string) $row['pictures'])));
        }

        return [];
    }
}
