<?php

namespace App\Http\Controllers\Seo;

use App\Catalog\Models\ShopifyProduct;
use App\Content\Models\Article;
use App\Content\Models\Page;
use App\Http\Controllers\Controller;
use App\Markets\Models\Market;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $markets = Market::query()->get();

        return response()
            ->view('seo.sitemap', [
                'products' => ShopifyProduct::query()->where('status', 'active')->get(),
                'pages' => Page::query()->whereNotNull('published_at')->get(),
                'articles' => Article::query()->whereNotNull('published_at')->get(),
                'markets' => $markets,
            ])
            ->header('Content-Type', 'application/xml');
    }
}
