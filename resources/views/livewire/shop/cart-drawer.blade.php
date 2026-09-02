<div>
    @if ($open)
        <div class="fixed inset-0 z-40 bg-black/40" wire:click="close"></div>
        <aside class="fixed inset-y-0 right-0 z-50 flex w-full max-w-md flex-col bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-black/10 px-5 py-4">
                <h2 class="text-lg font-semibold">Cart</h2>
                <button type="button" wire:click="close" class="text-sm text-muted">Close</button>
            </div>

            <div class="flex-1 overflow-y-auto px-5 py-4">
                @if ($error)
                    <p class="mb-4 text-sm text-red-700">{{ $error }}</p>
                @endif

                @forelse ($cart->lines as $line)
                    <div class="mb-4 flex gap-3 border-b border-black/5 pb-4">
                        <div class="h-16 w-16 overflow-hidden rounded-lg bg-hempe-light">
                            <x-shop.image :image="$line->image" />
                        </div>
                        <div class="flex-1">
                            <a href="{{ url('/products/'.$line->productHandle) }}" class="font-medium">{{ $line->title }}</a>
                            <p class="text-sm text-muted">{{ $line->amount->formatted() }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <button type="button" class="h-7 w-7 rounded-full border" wire:click="updateQuantity('{{ $line->id }}', {{ $line->quantity - 1 }})">−</button>
                                <span class="text-sm">{{ $line->quantity }}</span>
                                <button type="button" class="h-7 w-7 rounded-full border" wire:click="updateQuantity('{{ $line->id }}', {{ $line->quantity + 1 }})">+</button>
                                <button type="button" class="ml-auto text-xs text-muted" wire:click="remove('{{ $line->id }}')">Remove</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-muted">Your cart is empty.</p>
                @endforelse
            </div>

            <div class="border-t border-black/10 px-5 py-4">
                <div class="flex gap-2">
                    <input type="text" wire:model="discountCode" placeholder="Discount code" class="flex-1 rounded-full border border-black/15 px-3 py-2 text-sm">
                    <button type="button" wire:click="applyCode" class="text-sm text-hempe">Apply</button>
                </div>
                <div class="mt-4 flex items-center justify-between font-medium">
                    <span>Total</span>
                    <span>{{ $cart->total->formatted() }}</span>
                </div>
                <button type="button" wire:click="checkout" class="mt-4 w-full rounded-full bg-hempe py-3 text-sm font-semibold text-white" @disabled($cart->isEmpty())>
                    Checkout
                </button>
            </div>
        </aside>
    @endif
</div>
