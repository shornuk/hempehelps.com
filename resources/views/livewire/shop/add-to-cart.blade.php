<div class="space-y-3">
    <div class="flex items-center gap-3">
        <label class="sr-only" for="qty">Quantity</label>
        <input id="qty" type="number" min="1" wire:model.number="quantity" class="w-20 rounded-full border border-black/15 bg-white px-3 py-2">
        <button
            type="button"
            wire:click="add"
            wire:loading.attr="disabled"
            class="rounded-full bg-hempe px-6 py-3 text-sm font-semibold text-white disabled:opacity-60"
        >
            {{ $adding ? 'Adding…' : 'Add to cart' }}
        </button>
    </div>
    @if ($error)
        <p class="text-sm text-red-700">{{ $error }}</p>
    @endif
</div>
