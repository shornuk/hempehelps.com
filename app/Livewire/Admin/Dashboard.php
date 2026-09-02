<?php

namespace App\Livewire\Admin;

use App\Catalog\Models\ShopifyProduct;
use App\Content\Models\Page;
use App\Reviews\Models\Review;
use App\Seo\Models\Redirect;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Staff admin')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'products' => ShopifyProduct::query()->count(),
            'reviews' => Review::query()->count(),
            'pages' => Page::query()->count(),
            'redirects' => Redirect::query()->count(),
        ]);
    }
}
