<?php

namespace App\Http\Controllers\Shop;

use App\Catalog\Models\ShopifyProduct;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $products = ShopifyProduct::query()->where('status', 'active')->limit(8)->get();

        return view('shop.home', compact('products'));
    }
}
