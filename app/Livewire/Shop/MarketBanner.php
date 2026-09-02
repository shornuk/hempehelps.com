<?php

namespace App\Livewire\Shop;

use App\Markets\GeoSuggester;
use App\Markets\MarketContext;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class MarketBanner extends Component
{
    public bool $show = false;

    public string $suggestedHandle = '';

    public string $suggestedLabel = '';

    public string $suggestedUrl = '/';

    public function mount(MarketContext $market, GeoSuggester $suggester): void
    {
        $suggested = $suggester->suggestedMarket(request(), $market);

        if ($suggested === null) {
            $this->show = false;

            return;
        }

        $this->show = true;
        $this->suggestedHandle = $suggested->key;
        $this->suggestedLabel = strtoupper($suggested->key);
        $this->suggestedUrl = $suggested->path_prefix ? '/'.$suggested->path_prefix : '/';
    }

    public function dismiss(): void
    {
        session(['market_suggestion_dismissed' => true]);
        $this->show = false;
    }

    public function switchMarket()
    {
        Cookie::queue(
            config('markets.cookie'),
            $this->suggestedHandle,
            (int) config('markets.cookie_minutes'),
        );

        return redirect()->to($this->suggestedUrl);
    }

    public function render()
    {
        return view('livewire.shop.market-banner');
    }
}
