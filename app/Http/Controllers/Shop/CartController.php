<?php

namespace App\Http\Controllers\Shop;

use App\Cart\CartService;
use App\Http\Controllers\Controller;
use App\Shopify\Exceptions\CartExpiredException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(CartService $cart): View
    {
        return view('shop.cart', ['cart' => $cart->current()]);
    }

    public function checkout(Request $request, CartService $cart): RedirectResponse
    {
        try {
            $handoff = $cart->checkoutUrl($request->query('_gl'));
        } catch (CartExpiredException) {
            return redirect()->route('cart.show')->with('error', 'Your cart expired. Please add items again.');
        }

        return redirect()->away($handoff->url);
    }
}
