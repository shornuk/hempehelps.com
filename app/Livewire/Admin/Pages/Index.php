<?php

namespace App\Livewire\Admin\Pages;

use App\Content\Models\Article;
use App\Content\Models\Faq;
use App\Content\Models\Page;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Content')]
class Index extends Component
{
    public string $title = '';

    public string $slug = '';

    public string $body = '';

    public string $type = 'page';

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string'],
            'type' => ['required', 'in:page,article,faq'],
        ]);

        match ($data['type']) {
            'article' => Article::query()->updateOrCreate(
                ['slug' => $data['slug'], 'market_id' => null],
                ['title' => $data['title'], 'body' => $data['body'], 'published_at' => now()],
            ),
            'faq' => Faq::query()->create([
                'question' => $data['title'],
                'answer' => $data['body'],
                'position' => (int) Faq::query()->max('position') + 1,
            ]),
            default => Page::query()->updateOrCreate(
                ['slug' => $data['slug'], 'market_id' => null],
                ['title' => $data['title'], 'body' => $data['body'], 'published_at' => now()],
            ),
        };

        $this->reset(['title', 'slug', 'body']);
    }

    public function render(): View
    {
        return view('livewire.admin.pages.index', [
            'pages' => Page::query()->latest()->limit(50)->get(),
            'articles' => Article::query()->latest()->limit(50)->get(),
            'faqs' => Faq::query()->orderBy('position')->get(),
        ]);
    }
}
