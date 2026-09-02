<?php

namespace App\Livewire\Admin\Products;

use App\Catalog\Jobs\SyncCatalogJob;
use App\Catalog\Models\ProductContent;
use App\Catalog\Models\ShopifyProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Products')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editing = null;

    public string $meta_title = '';

    public string $meta_description = '';

    public string $blocks = '';

    public function edit(int $id): void
    {
        $product = ShopifyProduct::query()->with('content')->findOrFail($id);
        $this->editing = $id;
        $this->meta_title = (string) $product->content?->meta_title;
        $this->meta_description = (string) $product->content?->meta_description;
        $this->blocks = json_encode($product->content?->blocks ?? [], JSON_PRETTY_PRINT);
    }

    public function save(): void
    {
        $this->validate([
            'editing' => ['required', 'exists:shopify_products,id'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string'],
            'blocks' => ['nullable', 'string'],
        ]);

        $decoded = json_decode($this->blocks ?: '[]', true);

        ProductContent::query()->updateOrCreate(
            ['shopify_product_id' => $this->editing, 'market_id' => null],
            [
                'meta_title' => $this->meta_title ?: null,
                'meta_description' => $this->meta_description ?: null,
                'blocks' => is_array($decoded) ? $decoded : [],
            ],
        );

        $this->editing = null;
    }

    public function sync(): void
    {
        SyncCatalogJob::dispatch();
    }

    public function render()
    {
        $products = ShopifyProduct::query()
            ->with('content')
            ->when($this->search, fn ($query) => $query->where('title', 'like', '%'.$this->search.'%')
                ->orWhere('handle', 'like', '%'.$this->search.'%'))
            ->orderBy('title')
            ->paginate(25);

        return view('livewire.admin.products.index', compact('products'));
    }
}
