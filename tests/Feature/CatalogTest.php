<?php

namespace Tests\Feature;

use App\Catalog\Models\ShopifyProduct;
use App\Reviews\Models\Review;
use Database\Seeders\MarketSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_is_ok(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('HEMPE');
    }

    public function test_shop_lists_local_products(): void
    {
        ShopifyProduct::factory()->create(['title' => 'Hot Muscle Gel', 'handle' => 'cbd-hot-muscle-joint-gel']);

        $this->get('/shop')
            ->assertOk()
            ->assertSee('Hot Muscle Gel');
    }

    public function test_pdp_renders_schema_and_reviews(): void
    {
        $product = ShopifyProduct::factory()->create([
            'title' => 'Hot Muscle Gel',
            'handle' => 'cbd-hot-muscle-joint-gel',
        ]);
        Review::factory()->create([
            'shopify_product_id' => $product->id,
            'author_name' => 'Ailsa',
            'body' => 'Works on my knees.',
        ]);

        $this->get('/products/cbd-hot-muscle-joint-gel')
            ->assertOk()
            ->assertSee('Hot Muscle Gel')
            ->assertSee('application/ld+json', false)
            ->assertSee('AggregateRating', false)
            ->assertSee('Works on my knees.');
    }

    public function test_collections_all_redirects_to_shop(): void
    {
        $this->get('/collections/all')->assertRedirect('/shop');
    }

    public function test_market_prefix_keeps_shop_reachable(): void
    {
        $this->seed(MarketSeeder::class);

        $this->get('/en-us/shop')->assertOk();
    }

    public function test_sitemap_is_xml(): void
    {
        ShopifyProduct::factory()->create(['handle' => 'cbd-hot-muscle-joint-gel']);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('cbd-hot-muscle-joint-gel', false);
    }
}
