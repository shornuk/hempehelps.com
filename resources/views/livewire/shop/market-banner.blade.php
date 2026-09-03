<div>
    @if ($show)
        <div class="bg-hempe-dark px-4 py-2 text-center text-sm text-white">
            Shopping from {{ $suggestedLabel }}?
            <button type="button" class="underline" wire:click="switchMarket">Switch market</button>
            ·
            <button type="button" class="underline" wire:click="dismiss">Stay here</button>
        </div>
    @endif
</div>
