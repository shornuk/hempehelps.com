<?php

namespace App\Livewire\Shop;

use App\Cart\CartService;
use App\Shopify\Exceptions\CartExpiredException;
use App\Shopify\Exceptions\MissingCredentialsException;
use App\Shopify\Exceptions\ShopifyException;
use Livewire\Component;

class AddToCart extends Component
{
    public string $variantId = '';

    public int $quantity = 1;

    public bool $adding = false;

    public ?string $error = null;

    public function add(CartService $cart): void
    {
        $this->adding = true;
        $this->error = null;

        try {
            $cart->addLines([
                [
                    'merchandiseId' => $this->variantId,
                    'quantity' => max(1, $this->quantity),
                ],
            ]);

            $this->dispatch('cart-updated');
        } catch (MissingCredentialsException|CartExpiredException|ShopifyException $exception) {
            $this->error = $exception->getMessage();
        } finally {
            $this->adding = false;
        }
    }

    public function render()
    {
        return view('livewire.shop.add-to-cart');
    }
}
