<x-layouts.storefront title="Cart">
    <div class="mx-auto max-w-3xl px-4 py-12">
        <h1 class="text-3xl font-semibold">Your cart</h1>
        @if (session('error'))
            <p class="mt-4 text-sm text-red-700">{{ session('error') }}</p>
        @endif

        @if ($cart->isEmpty())
            <p class="mt-6 text-muted">Your cart is empty.</p>
            <a href="{{ url('/shop') }}" class="mt-4 inline-block text-hempe">Continue shopping</a>
        @else
            <ul class="mt-8 divide-y divide-black/10">
                @foreach ($cart->lines as $line)
                    <li class="flex items-center justify-between py-4">
                        <div>
                            <a href="{{ url('/products/'.$line->productHandle) }}" class="font-medium">{{ $line->title }}</a>
                            <p class="text-sm text-muted">Qty {{ $line->quantity }}</p>
                        </div>
                        <x-shop.price :money="$line->amount" />
                    </li>
                @endforeach
            </ul>
            <div class="mt-6 flex items-center justify-between font-medium">
                <span>Total</span>
                <x-shop.price :money="$cart->total" />
            </div>
            <a href="{{ route('checkout') }}" class="mt-8 inline-block rounded-full bg-hempe px-6 py-3 text-white">Checkout</a>
        @endif
    </div>
</x-layouts.storefront>
