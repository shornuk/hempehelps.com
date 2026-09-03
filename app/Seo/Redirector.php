<?php

namespace App\Seo;

use App\Markets\MarketContext;
use App\Markets\Models\Market;
use App\Seo\Models\Redirect;
use Illuminate\Http\Request;

class Redirector
{
    public function match(Request $request, MarketContext $market): ?Redirect
    {
        $path = '/'.ltrim($request->getPathInfo(), '/');

        if ($market->pathPrefix && ($path === '/'.$market->pathPrefix || str_starts_with($path, '/'.$market->pathPrefix.'/'))) {
            $path = substr($path, strlen('/'.$market->pathPrefix)) ?: '/';
        }

        return Redirect::query()
            ->where('from_path', $path)
            ->where(function ($query) use ($market): void {
                $query->whereNull('market_id')
                    ->orWhere('market_id', $this->marketId($market));
            })
            ->orderByRaw('market_id is null')
            ->first();
    }

    protected function marketId(MarketContext $market): ?int
    {
        return Market::query()->where('key', $market->key)->value('id');
    }
}
