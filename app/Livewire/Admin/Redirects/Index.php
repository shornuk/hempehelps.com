<?php

namespace App\Livewire\Admin\Redirects;

use App\Seo\Jobs\SyncShopifyRedirectsJob;
use App\Seo\Models\Redirect;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Redirects')]
class Index extends Component
{
    public string $from_path = '';

    public string $to_url = '';

    public function save(): void
    {
        $data = $this->validate([
            'from_path' => ['required', 'string', 'starts_with:/'],
            'to_url' => ['required', 'string'],
        ]);

        Redirect::query()->updateOrCreate(
            ['from_path' => $data['from_path'], 'market_id' => null],
            ['to_url' => $data['to_url'], 'status' => 301],
        );

        $this->reset(['from_path', 'to_url']);
    }

    public function delete(int $id): void
    {
        Redirect::query()->findOrFail($id)->delete();
    }

    public function sync(): void
    {
        SyncShopifyRedirectsJob::dispatch();
    }

    public function render(): View
    {
        return view('livewire.admin.redirects.index', [
            'redirects' => Redirect::query()->latest()->limit(200)->get(),
        ]);
    }
}
