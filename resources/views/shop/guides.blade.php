<x-layouts.storefront title="Guides">
    <div class="mx-auto max-w-3xl px-4 py-16">
        <h1 class="text-4xl font-semibold">Guides</h1>
        <ul class="mt-8 space-y-6">
            @forelse ($articles as $article)
                <li>
                    <a href="{{ url('/guides/'.$article->slug) }}" class="text-xl font-medium text-hempe">{{ $article->title }}</a>
                    @if ($article->excerpt)
                        <p class="mt-1 text-sm text-muted">{{ $article->excerpt }}</p>
                    @endif
                </li>
            @empty
                <li class="text-muted">Guides are being written.</li>
            @endforelse
        </ul>
    </div>
</x-layouts.storefront>
