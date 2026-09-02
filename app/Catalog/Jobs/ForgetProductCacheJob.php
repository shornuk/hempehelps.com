<?php

namespace App\Catalog\Jobs;

use App\Markets\Models\Market;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class ForgetProductCacheJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $handle) {}

    public function handle(): void
    {
        foreach (Market::query()->pluck('key') as $key) {
            Cache::forget('product:'.$this->handle.':'.$key);
        }
    }
}
