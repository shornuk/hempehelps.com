<?php

namespace App\Livewire\Admin\Reviews;

use App\Enums\ReviewStatus;
use App\Reviews\Models\Review;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Reviews')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function toggle(int $id): void
    {
        $review = Review::query()->findOrFail($id);
        $review->status = $review->status === ReviewStatus::Published
            ? ReviewStatus::Hidden
            : ReviewStatus::Published;
        $review->save();
    }

    public function delete(int $id): void
    {
        Review::query()->findOrFail($id)->delete();
    }

    public function render(): View
    {
        $reviews = Review::query()
            ->with('product')
            ->when($this->search, fn ($query) => $query->where(function ($inner): void {
                $inner->where('author_name', 'like', '%'.$this->search.'%')
                    ->orWhere('body', 'like', '%'.$this->search.'%')
                    ->orWhereHas('product', fn ($product) => $product->where('title', 'like', '%'.$this->search.'%'));
            }))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.reviews.index', compact('reviews'));
    }
}
