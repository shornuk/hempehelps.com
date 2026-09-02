<div class="max-w-2xl space-y-4">
    <flux:heading size="xl">{{ $review ? 'Edit review' : 'Add review' }}</flux:heading>

    <flux:field>
        <flux:label>Product</flux:label>
        <flux:input wire:model.live.debounce.300ms="productSearch" placeholder="Search title or handle" />
        @if ($matches)
            <div class="mt-2 space-y-1">
                @foreach ($matches as $match)
                    <flux:button size="sm" variant="ghost" wire:click="selectProduct({{ $match->id }})">
                        {{ $match->title }} ({{ $match->handle }})
                    </flux:button>
                @endforeach
            </div>
        @endif
        <flux:error name="shopify_product_id" />
    </flux:field>

    <flux:field>
        <flux:label>Author</flux:label>
        <flux:input wire:model="author_name" />
        <flux:error name="author_name" />
    </flux:field>

    <flux:field>
        <flux:label>Email</flux:label>
        <flux:input wire:model="author_email" type="email" />
    </flux:field>

    <flux:field>
        <flux:label>Rating</flux:label>
        <flux:select wire:model="rating">
            @foreach (range(1, 5) as $value)
                <flux:select.option :value="$value">{{ $value }}</flux:select.option>
            @endforeach
        </flux:select>
    </flux:field>

    <flux:field>
        <flux:label>Title</flux:label>
        <flux:input wire:model="title" />
    </flux:field>

    <flux:field>
        <flux:label>Body</flux:label>
        <flux:textarea wire:model="body" rows="6" />
        <flux:error name="body" />
    </flux:field>

    <flux:field>
        <flux:label>Status</flux:label>
        <flux:select wire:model="status">
            <flux:select.option value="draft">Draft</flux:select.option>
            <flux:select.option value="published">Published</flux:select.option>
            <flux:select.option value="hidden">Hidden</flux:select.option>
        </flux:select>
    </flux:field>

    <flux:checkbox wire:model="verified_purchase" label="Verified purchase" />

    <flux:button wire:click="save">Save</flux:button>
</div>
