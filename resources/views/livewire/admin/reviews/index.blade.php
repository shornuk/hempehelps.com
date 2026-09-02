<div class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Reviews</flux:heading>
        <flux:button :href="route('admin.reviews.create')" wire:navigate>Add review</flux:button>
    </div>

    <flux:input wire:model.live.debounce.300ms="search" placeholder="Search author, body, or product" />

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Product</flux:table.column>
            <flux:table.column>Author</flux:table.column>
            <flux:table.column>Rating</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($reviews as $review)
                <flux:table.row :key="$review->id">
                    <flux:table.cell>{{ $review->product?->title }}</flux:table.cell>
                    <flux:table.cell>{{ $review->author_name }}</flux:table.cell>
                    <flux:table.cell>{{ $review->rating }}</flux:table.cell>
                    <flux:table.cell>{{ $review->status->value }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            <flux:button size="sm" :href="route('admin.reviews.edit', $review)" wire:navigate>Edit</flux:button>
                            <flux:button size="sm" variant="ghost" wire:click="toggle({{ $review->id }})">Toggle</flux:button>
                            <flux:button size="sm" variant="danger" wire:click="delete({{ $review->id }})">Delete</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    {{ $reviews->links() }}
</div>
