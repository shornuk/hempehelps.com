<div class="space-y-6">
    <flux:heading size="xl">Hemp House admin</flux:heading>
    <flux:text>Catalogue, reviews, redirects, and editorial. Flux stays here.</flux:text>

    <div class="grid gap-4 md:grid-cols-4">
        <flux:card class="p-4">
            <flux:heading size="lg">{{ $products }}</flux:heading>
            <flux:text>Products</flux:text>
        </flux:card>
        <flux:card class="p-4">
            <flux:heading size="lg">{{ $reviews }}</flux:heading>
            <flux:text>Reviews</flux:text>
        </flux:card>
        <flux:card class="p-4">
            <flux:heading size="lg">{{ $pages }}</flux:heading>
            <flux:text>Pages</flux:text>
        </flux:card>
        <flux:card class="p-4">
            <flux:heading size="lg">{{ $redirects }}</flux:heading>
            <flux:text>Redirects</flux:text>
        </flux:card>
    </div>
</div>
