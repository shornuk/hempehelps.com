<?php

namespace App\Cart;

use Illuminate\Contracts\Session\Session;

class CartSessionStore
{
    public const SESSION_KEY = 'shopify_cart_id';

    public function __construct(private readonly Session $session) {}

    public function get(): ?string
    {
        $id = $this->session->get(self::SESSION_KEY);

        return is_string($id) && $id !== '' ? $id : null;
    }

    public function put(string $cartId): void
    {
        $this->session->put(self::SESSION_KEY, $cartId);
    }

    public function forget(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }
}
