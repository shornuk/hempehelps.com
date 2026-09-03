@props(['reviews', 'summary' => null])

<section class="space-y-6">
    <header class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold">Reviews</h2>
            @if ($summary && $summary->review_count)
                <p class="mt-1 text-sm text-muted">
                    <x-reviews.stars :rating="$summary->average_rating" />
                    {{ number_format($summary->average_rating, 1) }} · {{ $summary->review_count }} reviews
                </p>
            @endif
        </div>
    </header>

    @forelse ($reviews as $review)
        <article class="border-t border-black/10 pt-4">
            <div class="flex items-center justify-between gap-4">
                <strong>{{ $review->author_name }}</strong>
                <x-reviews.stars :rating="$review->rating" />
            </div>
            @if ($review->title)
                <h3 class="mt-1 font-medium">{{ $review->title }}</h3>
            @endif
            <p class="mt-2 text-sm leading-relaxed text-muted">{{ $review->body }}</p>
        </article>
    @empty
        <p class="text-sm text-muted">No published reviews yet.</p>
    @endforelse
</section>
