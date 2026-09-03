<?php

namespace App\Http\Controllers\Shop;

use App\Catalog\DTOs\Collection;
use App\Catalog\DTOs\Product;
use App\Catalog\Models\ShopifyCollection;
use App\Catalog\Models\ShopifyProduct;
use App\Catalog\ProductRepository;
use App\Http\Controllers\Controller;
use App\Seo\Schema;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(ProductRepository $products): View
    {
        $collections = ShopifyCollection::query()->orderBy('title')->get();

        return view('shop.catalog', [
            'products' => $products->getProducts(),
            'collections' => $collections,
            'collection' => null,
        ]);
    }

    public function collection(string $handle, ProductRepository $products): View
    {
        $collection = $products->getCollectionByHandle($handle);

        abort_unless($collection instanceof Collection, 404);

        return view('shop.catalog', [
            'products' => $collection->products,
            'collections' => ShopifyCollection::query()->orderBy('title')->get(),
            'collection' => $collection,
        ]);
    }

    public function show(string $handle, ProductRepository $products): View
    {
        $product = $products->getProductByHandle($handle);

        abort_unless($product instanceof Product, 404);

        $local = ShopifyProduct::query()->where('handle', $handle)->first();

        $reviews = $local?->reviews()->published()->latest()->get() ?? collect();

        return view('shop.product', [
            'product' => $product,
            'local' => $local,
            'reviews' => $reviews,
            'summary' => $local?->reviewSummary,
            'content' => $local?->content,
            'schema' => app(Schema::class)->product($product, $local?->reviewSummary, $reviews),
        ]);
    }
}
