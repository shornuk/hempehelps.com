<div class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Redirects</flux:heading>
        <flux:button variant="ghost" wire:click="sync">Sync from Shopify</flux:button>
    </div>

    <div class="grid gap-3 md:grid-cols-3">
        <flux:input wire:model="from_path" placeholder="/old-path" />
        <flux:input wire:model="to_url" placeholder="/new-path" />
        <flux:button wire:click="save">Add 301</flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>From</flux:table.column>
            <flux:table.column>To</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($redirects as $redirect)
                <flux:table.row :key="$redirect->id">
                    <flux:table.cell>{{ $redirect->from_path }}</flux:table.cell>
                    <flux:table.cell>{{ $redirect->to_url }}</flux:table.cell>
                    <flux:table.cell>{{ $redirect->status }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" variant="ghost" wire:click="delete({{ $redirect->id }})">Remove</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
