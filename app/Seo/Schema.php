<?php

namespace App\Seo;

use App\Catalog\DTOs\Product;
use App\Content\Models\Article;
use App\Reviews\Models\Review;
use App\Reviews\Models\ReviewSummary;
use Illuminate\Support\Collection;

class Schema
{
    /**
     * @param  Collection<int, Review>  $reviews
     * @return array<string, mixed>
     */
    public function product(Product $product, ?ReviewSummary $summary = null, Collection $reviews = new Collection): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->title,
            'description' => strip_tags($product->descriptionHtml),
            'sku' => $product->selectedVariant->sku,
            'image' => array_map(fn ($image) => $image->url, $product->images),
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => $product->selectedVariant->price->currencyCode,
                'price' => $product->selectedVariant->price->amount,
                'availability' => $product->available
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'url' => url('/products/'.$product->handle),
            ],
        ];

        if ($summary && $summary->review_count > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $summary->average_rating,
                'reviewCount' => $summary->review_count,
            ];
        }

        if ($reviews->isNotEmpty()) {
            $schema['review'] = $reviews->map(fn ($review): array => [
                '@type' => 'Review',
                'author' => ['@type' => 'Person', 'name' => $review->author_name],
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => $review->rating],
                'reviewBody' => $review->body,
            ])->all();
        }

        return $schema;
    }

    /**
     * @return array<string, mixed>
     */
    public function article(Article $article): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title,
            'description' => $article->excerpt,
            'datePublished' => $article->published_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'HEMPE',
            'url' => url('/'),
        ];
    }
}
