<x-layouts.storefront :title="$page->meta_title ?: $page->title" :description="$page->meta_description">
    <article class="mx-auto max-w-3xl px-4 py-16">
        <h1 class="text-4xl font-semibold">{{ $page->title }}</h1>
        <div class="prose mt-8 max-w-none">{!! $page->body !!}</div>
    </article>
</x-layouts.storefront>
