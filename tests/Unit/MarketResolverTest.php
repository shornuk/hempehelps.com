<?php

namespace Tests\Unit;

use App\Markets\MarketResolver;
use Database\Seeders\MarketSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class MarketResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_path_prefix(): void
    {
        $this->seed(MarketSeeder::class);

        $context = app(MarketResolver::class)->resolve(
            Request::create('https://hempehelps.com/en-us/shop', 'GET'),
        );

        $this->assertSame('us', $context->key);
        $this->assertSame('USD', $context->currency);
        $this->assertSame('en-us', $context->pathPrefix);
    }

    public function test_defaults_to_gb(): void
    {
        $this->seed(MarketSeeder::class);

        $context = app(MarketResolver::class)->resolve(
            Request::create('https://new.hempehelps.test/shop', 'GET'),
        );

        $this->assertSame('gb', $context->key);
        $this->assertTrue($context->isDefault);
    }
}
