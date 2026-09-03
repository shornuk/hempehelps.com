<div class="space-y-8">
    <flux:heading size="xl">Editorial</flux:heading>

    <div class="grid gap-4 md:grid-cols-2">
        <flux:field>
            <flux:label>Type</flux:label>
            <flux:select wire:model="type">
                <flux:select.option value="page">Page</flux:select.option>
                <flux:select.option value="article">Guide</flux:select.option>
                <flux:select.option value="faq">FAQ</flux:select.option>
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:label>Title / question</flux:label>
            <flux:input wire:model="title" />
        </flux:field>
        <flux:field>
            <flux:label>Slug</flux:label>
            <flux:input wire:model="slug" />
        </flux:field>
        <flux:field class="md:col-span-2">
            <flux:label>Body / answer</flux:label>
            <flux:textarea wire:model="body" rows="6" />
        </flux:field>
    </div>
    <flux:button wire:click="save">Publish</flux:button>

    <div class="grid gap-6 md:grid-cols-3">
        <div>
            <flux:heading size="lg">Pages</flux:heading>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($pages as $page)
                    <li>{{ $page->title }} — /pages/{{ $page->slug }}</li>
                @endforeach
            </ul>
        </div>
        <div>
            <flux:heading size="lg">Guides</flux:heading>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($articles as $article)
                    <li>{{ $article->title }} — /guides/{{ $article->slug }}</li>
                @endforeach
            </ul>
        </div>
        <div>
            <flux:heading size="lg">FAQ</flux:heading>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($faqs as $faq)
                    <li>{{ $faq->question }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
