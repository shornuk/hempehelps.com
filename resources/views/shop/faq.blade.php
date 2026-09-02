<x-layouts.storefront title="FAQ">
    <div class="mx-auto max-w-3xl px-4 py-16">
        <h1 class="text-4xl font-semibold">FAQ</h1>
        <div class="mt-8 divide-y divide-black/10">
            @forelse ($faqs as $faq)
                <details class="py-4">
                    <summary class="cursor-pointer font-medium">{{ $faq->question }}</summary>
                    <div class="prose mt-3 max-w-none text-muted">{!! $faq->answer !!}</div>
                </details>
            @empty
                <p class="text-muted">No FAQs published yet.</p>
            @endforelse
        </div>
    </div>
</x-layouts.storefront>
