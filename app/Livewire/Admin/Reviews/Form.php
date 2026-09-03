<?php

namespace App\Livewire\Admin\Reviews;

use App\Catalog\Models\ShopifyProduct;
use App\Enums\ReviewSource;
use App\Enums\ReviewStatus;
use App\Reviews\Models\Review;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Review')]
class Form extends Component
{
    public ?Review $review = null;

    public string $productSearch = '';

    public ?int $shopify_product_id = null;

    public string $author_name = '';

    public string $author_email = '';

    public int $rating = 5;

    public string $title = '';

    public string $body = '';

    public string $status = 'published';

    public bool $verified_purchase = false;

    public function mount(?Review $review = null): void
    {
        $this->review = $review?->exists ? $review : null;

        if ($this->review) {
            $this->shopify_product_id = $this->review->shopify_product_id;
            $this->author_name = $this->review->author_name;
            $this->author_email = (string) $this->review->author_email;
            $this->rating = $this->review->rating;
            $this->title = (string) $this->review->title;
            $this->body = $this->review->body;
            $this->status = $this->review->status->value;
            $this->verified_purchase = $this->review->verified_purchase;
            $this->productSearch = $this->review->product->title;
        }
    }

    public function selectProduct(int $id): void
    {
        $product = ShopifyProduct::query()->findOrFail($id);
        $this->shopify_product_id = $product->id;
        $this->productSearch = $product->title;
    }

    public function save(): void
    {
        $data = $this->validate([
            'shopify_product_id' => ['required', 'exists:shopify_products,id'],
            'author_name' => ['required', 'string', 'max:120'],
            'author_email' => ['nullable', 'email'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:180'],
            'body' => ['required', 'string'],
            'status' => ['required', 'in:draft,published,hidden'],
            'verified_purchase' => ['boolean'],
        ]);

        $payload = [
            ...$data,
            'source' => $this->review instanceof Review ? $this->review->source : ReviewSource::Admin,
            'published_at' => $data['status'] === ReviewStatus::Published->value ? now() : $this->review?->published_at,
        ];

        if ($this->review) {
            $this->review->update($payload);
        } else {
            Review::query()->create($payload);
        }

        $this->redirect(route('admin.reviews.index'), navigate: true);
    }

    public function render(): View
    {
        $matches = [];

        if (strlen($this->productSearch) >= 2 && ! $this->shopify_product_id) {
            $matches = ShopifyProduct::query()
                ->where('title', 'like', '%'.$this->productSearch.'%')
                ->orWhere('handle', 'like', '%'.$this->productSearch.'%')
                ->limit(8)
                ->get();
        }

        return view('livewire.admin.reviews.form', [
            'matches' => $matches,
        ]);
    }
}
