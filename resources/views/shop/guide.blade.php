<x-layouts.storefront :title="$article->meta_title ?: $article->title" :description="$article->excerpt" :schema="app(\App\Seo\Schema::class)->article($article)">
    <article class="mx-auto max-w-3xl px-4 py-16">
        <p class="text-sm uppercase tracking-[0.2em] text-hempe">Guide</p>
        <h1 class="mt-2 text-4xl font-semibold">{{ $article->title }}</h1>
        <div class="prose mt-8 max-w-none">{!! $article->body !!}</div>
    </article>
</x-layouts.storefront>
