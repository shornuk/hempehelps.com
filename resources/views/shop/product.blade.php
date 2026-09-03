<x-layouts.storefront
    :title="$content?->meta_title ?: $product->title"
    :description="$content?->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->descriptionHtml), 160)"
    :schema="$schema"
>
    <article class="mx-auto grid max-w-6xl gap-12 px-4 py-12 lg:grid-cols-2">
        <div class="space-y-4">
            <x-shop.image :image="$product->featuredImage ?? $product->images[0] ?? null" class="aspect-square rounded-2xl bg-white" sizes="(min-width: 1024px) 40vw, 100vw" />
            @if (count($product->images) > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach ($product->images as $image)
                        <x-shop.image :image="$image" class="aspect-square rounded-lg" />
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-hempe">{{ $product->productType ?: 'HEMPE' }}</p>
            <h1 class="mt-2 text-4xl font-semibold">{{ $product->title }}</h1>
            <div class="mt-4 flex items-center gap-3">
                <x-shop.price :money="$product->selectedVariant->price" :compare="$product->selectedVariant->compareAtPrice" class="text-xl text-hempe" />
                @if ($summary?->review_count)
                    <x-reviews.stars :rating="$summary->average_rating" />
                @endif
            </div>

            <div class="prose mt-6 max-w-none text-muted">{!! $product->descriptionHtml !!}</div>

            @if (! empty($content?->blocks['benefits']))
                <ul class="mt-6 list-disc space-y-1 pl-5 text-sm">
                    @foreach ($content->blocks['benefits'] as $benefit)
                        <li>{{ $benefit }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-8">
                <livewire:shop.add-to-cart :variant-id="$product->selectedVariant->id" :key="$product->id" />
            </div>
        </div>
    </article>

    <div class="mx-auto max-w-6xl px-4 pb-16">
        <x-reviews.list :reviews="$reviews" :summary="$summary" />
    </div>
</x-layouts.storefront>
