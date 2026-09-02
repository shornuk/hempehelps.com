<x-layouts.storefront :title="$collection?->title ?? 'Shop'" :description="$collection?->title ? 'Shop '.$collection->title : 'Shop HEMPE products.'">
    <div class="mx-auto max-w-6xl px-4 py-12">
        <p class="text-sm uppercase tracking-[0.2em] text-hempe">Catalogue</p>
        <h1 class="mt-2 text-4xl font-semibold">{{ $collection?->title ?? 'Shop' }}</h1>

        @if ($collections->isNotEmpty())
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ url('/shop') }}" class="rounded-full px-3 py-1 text-sm {{ $collection ? 'bg-white' : 'bg-hempe text-white' }}">All</a>
                @foreach ($collections as $item)
                    <a href="{{ url('/shop/'.$item->handle) }}" class="rounded-full px-3 py-1 text-sm {{ $collection?->handle === $item->handle ? 'bg-hempe text-white' : 'bg-white' }}">
                        {{ $item->title }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($products as $product)
                <x-shop.product-card :product="$product" />
            @empty
                <p class="text-muted">No products published to this catalogue yet.</p>
            @endforelse
        </div>
    </div>
</x-layouts.storefront>
