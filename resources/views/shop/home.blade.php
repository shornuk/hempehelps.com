<x-layouts.storefront title="HEMPEhelps" description="Plant-powered recovery from HEMPE." :schema="app(\App\Seo\Schema::class)->organization()">
    <section class="bg-hempe text-white">
        <div class="mx-auto max-w-6xl px-4 py-20">
            <p class="text-sm uppercase tracking-[0.2em]">HEMPEhelps</p>
            <h1 class="mt-4 max-w-2xl text-4xl font-semibold leading-tight md:text-6xl">Recovery that starts with plants, not theatre.</h1>
            <p class="mt-6 max-w-xl text-lg text-white/80">Hot gel, ice gel, and the everyday kit our customers actually finish.</p>
            <a href="{{ url('/shop') }}" class="mt-8 inline-block rounded-full bg-white px-6 py-3 text-sm font-semibold text-hempe">Shop HEMPE</a>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="mb-8 flex items-end justify-between">
            <h2 class="text-2xl font-semibold">Featured</h2>
            <a href="{{ url('/shop') }}" class="text-sm text-hempe">View all</a>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($products as $product)
                <a href="{{ url('/products/'.$product->handle) }}" class="rounded-xl bg-white p-3">
                    @if ($product->featured_image_url)
                        <img src="{{ $product->featured_image_url }}" alt="{{ $product->title }}" class="aspect-square w-full rounded-lg object-cover">
                    @else
                        <div class="flex aspect-square items-center justify-center rounded-lg bg-hempe-light text-hempe">HEMPE</div>
                    @endif
                    <h3 class="mt-3 font-medium">{{ $product->title }}</h3>
                </a>
            @endforeach
        </div>
    </section>
</x-layouts.storefront>
