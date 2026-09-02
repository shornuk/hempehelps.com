<?php

namespace Tests\Unit;

use App\Markets\MarketContext;
use App\Seo\Models\Redirect;
use App\Seo\Redirector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class RedirectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_matches_from_path(): void
    {
        Redirect::factory()->create([
            'from_path' => '/collections/all',
            'to_url' => '/shop',
        ]);

        $match = app(Redirector::class)->match(
            Request::create('/collections/all', 'GET'),
            MarketContext::default(),
        );

        $this->assertSame('/shop', $match?->to_url);
    }
}
