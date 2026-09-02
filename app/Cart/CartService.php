<?php

namespace App\Cart;

use App\Cart\DTOs\Cart;
use App\Cart\DTOs\CheckoutHandoff;
use App\Catalog\DTOs\Money;
use App\Markets\MarketContext;
use App\Shopify\Exceptions\CartExpiredException;
use App\Shopify\Exceptions\MissingCredentialsException;
use App\Shopify\Mappers\CartMapper;
use App\Shopify\StorefrontClient;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function __construct(
        private readonly StorefrontClient $storefront,
        private readonly CartSessionStore $store,
        private readonly CartMapper $mapper,
        private readonly MarketContext $market,
    ) {}

    public function current(): Cart
    {
        $id = $this->store->get();

        if ($id === null) {
            return $this->emptyCart();
        }

        try {
            $data = $this->storefront->query($this->storefront->document('get-cart'), ['id' => $id]);
        } catch (MissingCredentialsException) {
            return $this->emptyCart();
        } catch (CartExpiredException) {
            $this->store->forget();

            return $this->emptyCart();
        }

        if (empty($data['cart'])) {
            $this->store->forget();

            return $this->emptyCart();
        }

        return $this->mapper->map($data['cart'], $this->market->currency);
    }

    /**
     * @param  array<int, array{merchandiseId: string, quantity: int}>  $lines
     */
    public function addLines(array $lines): Cart
    {
        $id = $this->store->get();

        if ($id === null) {
            return $this->create($lines);
        }

        try {
            $data = $this->storefront->query($this->storefront->document('cart-lines-add'), [
                'cartId' => $id,
                'lines' => $lines,
            ]);
        } catch (CartExpiredException) {
            $this->store->forget();

            return $this->create($lines);
        }

        $cart = $data['cartLinesAdd']['cart'] ?? null;

        if (! is_array($cart)) {
            $this->store->forget();

            return $this->create($lines);
        }

        return $this->mapper->map($cart, $this->market->currency);
    }

    /**
     * @param  array<int, array{id: string, quantity: int}>  $lines
     */
    public function updateLines(array $lines): Cart
    {
        $id = $this->requireId();
        $data = $this->storefront->query($this->storefront->document('cart-lines-update'), [
            'cartId' => $id,
            'lines' => $lines,
        ]);

        return $this->mapper->map($data['cartLinesUpdate']['cart'], $this->market->currency);
    }

    /**
     * @param  list<string>  $lineIds
     */
    public function removeLines(array $lineIds): Cart
    {
        $id = $this->requireId();
        $data = $this->storefront->query($this->storefront->document('cart-lines-remove'), [
            'cartId' => $id,
            'lineIds' => $lineIds,
        ]);

        return $this->mapper->map($data['cartLinesRemove']['cart'], $this->market->currency);
    }

    public function applyDiscountCode(string $code): Cart
    {
        if ($this->store->get() === null) {
            $this->create([]);
        }

        $id = $this->requireId();
        $codes = array_values(array_unique(array_filter([
            ...$this->current()->discountCodes,
            trim($code),
        ])));

        $data = $this->storefront->query($this->storefront->document('cart-discount-codes-update'), [
            'cartId' => $id,
            'discountCodes' => $codes,
        ]);

        $cart = $data['cartDiscountCodesUpdate']['cart'] ?? null;

        if (! is_array($cart)) {
            throw new CartExpiredException('Unable to apply that discount code.');
        }

        return $this->mapper->map($cart, $this->market->currency);
    }

    public function checkoutUrl(?string $gl = null): CheckoutHandoff
    {
        $id = $this->requireId();
        $data = $this->storefront->query($this->storefront->document('checkout-url'), ['id' => $id]);
        $url = (string) ($data['cart']['checkoutUrl'] ?? '');

        if ($url === '') {
            throw new CartExpiredException('Checkout URL was not returned.');
        }

        if ($gl) {
            $url .= (str_contains($url, '?') ? '&' : '?').'_gl='.urlencode($gl);
        }

        return new CheckoutHandoff($url);
    }

    /**
     * @param  array<int, array{merchandiseId: string, quantity: int}>  $lines
     */
    public function create(array $lines): Cart
    {
        $attributes = [];

        if ($ref = session('affiliate_ref')) {
            $attributes[] = ['key' => 'collabs_ref', 'value' => (string) $ref];
        }

        $data = $this->storefront->query($this->storefront->document('cart-create'), [
            'input' => [
                'lines' => $lines,
                'buyerIdentity' => array_filter([
                    'countryCode' => $this->market->shopifyCountry,
                    'email' => Auth::user()?->email,
                ]),
                'attributes' => $attributes,
            ],
        ]);

        $cart = $data['cartCreate']['cart'] ?? null;

        if (! is_array($cart) || empty($cart['id'])) {
            throw new CartExpiredException('Unable to create Shopify cart.');
        }

        $this->store->put((string) $cart['id']);

        return $this->mapper->map($cart, $this->market->currency);
    }

    protected function requireId(): string
    {
        $id = $this->store->get();

        if ($id === null) {
            throw new CartExpiredException('No cart is stored for this session.');
        }

        return $id;
    }

    protected function emptyCart(): Cart
    {
        return new Cart(
            totalQuantity: 0,
            subtotal: new Money('0.00', $this->market->currency),
            total: new Money('0.00', $this->market->currency),
            lines: [],
        );
    }
}
