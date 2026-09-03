<div class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Products</flux:heading>
        <flux:button variant="ghost" wire:click="sync">Sync catalogue</flux:button>
    </div>
    <flux:text>Shopify-owned fields are read-only. Edit merchandising blocks and SEO here.</flux:text>

    <flux:input wire:model.live.debounce.300ms="search" placeholder="Search handle or title" />

    @if ($editing)
        <flux:card class="space-y-3 p-4">
            <flux:heading size="lg">Merchandising</flux:heading>
            <flux:input wire:model="meta_title" placeholder="Meta title" />
            <flux:textarea wire:model="meta_description" placeholder="Meta description" />
            <flux:textarea wire:model="blocks" rows="8" />
            <flux:button wire:click="save">Save content</flux:button>
        </flux:card>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>Handle</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($products as $product)
                <flux:table.row :key="$product->id">
                    <flux:table.cell>{{ $product->title }}</flux:table.cell>
                    <flux:table.cell>{{ $product->handle }}</flux:table.cell>
                    <flux:table.cell>{{ $product->status }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" wire:click="edit({{ $product->id }})">Content</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    {{ $products->links() }}
</div>
