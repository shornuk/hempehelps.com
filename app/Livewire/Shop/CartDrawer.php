<?php

namespace App\Livewire\Shop;

use App\Cart\CartService;
use App\Cart\DTOs\Cart;
use App\Catalog\DTOs\Money;
use App\Shopify\Exceptions\CartExpiredException;
use App\Shopify\Exceptions\MissingCredentialsException;
use App\Shopify\Exceptions\ShopifyException;
use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $open = false;

    public string $discountCode = '';

    public ?string $error = null;

    #[On('cart-updated')]
    #[On('open-cart')]
    public function openDrawer(): void
    {
        $this->open = true;
        $this->error = null;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function updateQuantity(string $lineId, int $quantity, CartService $cart): void
    {
        try {
            if ($quantity < 1) {
                $cart->removeLines([$lineId]);
            } else {
                $cart->updateLines([['id' => $lineId, 'quantity' => $quantity]]);
            }

            $this->error = null;
        } catch (MissingCredentialsException|CartExpiredException|ShopifyException $exception) {
            $this->error = $exception->getMessage();
        }
    }

    public function remove(string $lineId, CartService $cart): void
    {
        $this->updateQuantity($lineId, 0, $cart);
    }

    public function applyCode(CartService $cart): void
    {
        try {
            $cart->applyDiscountCode($this->discountCode);
            $this->discountCode = '';
            $this->error = null;
        } catch (MissingCredentialsException|CartExpiredException|ShopifyException $exception) {
            $this->error = $exception->getMessage();
        }
    }

    public function checkout(CartService $cart)
    {
        try {
            $gl = request()->query('_gl');
            $handoff = $cart->checkoutUrl(is_string($gl) ? $gl : null);

            return redirect()->away($handoff->url);
        } catch (MissingCredentialsException|CartExpiredException|ShopifyException $exception) {
            $this->error = $exception->getMessage();
        }
    }

    public function render(CartService $cart)
    {
        return view('livewire.shop.cart-drawer', [
            'cart' => $this->safeCart($cart),
        ]);
    }

    private function safeCart(CartService $cart): Cart
    {
        try {
            return $cart->current();
        } catch (MissingCredentialsException|CartExpiredException|ShopifyException) {
            return new Cart(
                totalQuantity: 0,
                subtotal: new Money('0.00', 'GBP'),
                total: new Money('0.00', 'GBP'),
                lines: [],
            );
        }
    }
}
