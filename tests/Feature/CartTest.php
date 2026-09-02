<?php

namespace Tests\Feature;

use App\Cart\CartSessionStore;
use App\Livewire\Shop\AddToCart;
use App\Livewire\Shop\CartDrawer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\FakesShopify;
use Tests\TestCase;

class CartTest extends TestCase
{
    use FakesShopify;
    use RefreshDatabase;

    public function test_cart_gid_is_not_leaked_to_html(): void
    {
        $id = 'gid://shopify/Cart/99?key=super-secret-key';

        $this->withSession([CartSessionStore::SESSION_KEY => $id])
            ->get('/')
            ->assertOk()
            ->assertDontSee($id, false)
            ->assertDontSee('super-secret-key', false)
            ->assertDontSee('gid://shopify/Cart/', false);
    }

    public function test_add_to_cart_stores_session_id_without_echoing_it(): void
    {
        $cart = $this->cartPayload();
        $this->fakeStorefront([
            'cartCreate' => ['cart' => $cart, 'userErrors' => []],
        ]);

        Livewire::test(AddToCart::class, ['variantId' => 'gid://shopify/ProductVariant/1'])
            ->call('add')
            ->assertDispatched('cart-updated')
            ->assertDontSee('secret-cart-key', false);

        $this->assertSame($cart['id'], session(CartSessionStore::SESSION_KEY));
    }

    public function test_cart_drawer_does_not_render_cart_gid(): void
    {
        $cart = $this->cartPayload();
        $this->fakeStorefront(['cart' => $cart]);
        session([CartSessionStore::SESSION_KEY => $cart['id']]);

        Livewire::test(CartDrawer::class)
            ->call('openDrawer')
            ->assertSee('Hot Gel')
            ->assertDontSee('secret-cart-key', false)
            ->assertDontSee('gid://shopify/Cart/', false);
    }
}
