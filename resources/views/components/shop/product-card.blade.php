@props(['product'])

<a href="{{ url('/products/'.$product->handle) }}" class="group block overflow-hidden rounded-xl bg-white">
    <div class="aspect-square overflow-hidden bg-hempe-light">
        <x-shop.image :image="$product->featuredImage" class="transition duration-300 group-hover:scale-105" />
    </div>
    <div class="space-y-1 p-4">
        <h2 class="text-base font-medium">{{ $product->title }}</h2>
        <x-shop.price :money="$product->selectedVariant->price" :compare="$product->selectedVariant->compareAtPrice" class="text-sm text-hempe" />
    </div>
</a>
