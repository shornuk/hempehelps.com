<?php

namespace App\Http\Controllers\Shop;

use App\Content\Models\Article;
use App\Content\Models\Faq;
use App\Content\Models\Page;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->firstOrFail();

        return view('shop.page', compact('page'));
    }

    public function guides(): View
    {
        $articles = Article::query()->whereNotNull('published_at')->latest('published_at')->get();

        return view('shop.guides', compact('articles'));
    }

    public function guide(string $slug): View
    {
        $article = Article::query()
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->firstOrFail();

        return view('shop.guide', compact('article'));
    }

    public function faq(): View
    {
        $faqs = Faq::query()->orderBy('position')->get();

        return view('shop.faq', compact('faqs'));
    }
}
