<?php

namespace App\Http\Controllers\Shop;

use App\Cart\CartService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class DiscountController extends Controller
{
    public function __invoke(string $code, Request $request, CartService $cart): RedirectResponse
    {
        $request->session()->put('affiliate_ref', $request->query('dt_id', $code));
        cookie()->queue(cookie('affiliate_ref', $code, 60 * 24 * 30));

        try {
            $cart->applyDiscountCode($code);
        } catch (Throwable) {
            // Collabs still persist the referral cookie even if Shopify is unreachable.
        }

        $redirect = $request->query('redirect', '/');

        return redirect()->to(is_string($redirect) && str_starts_with($redirect, '/') ? $redirect : '/');
    }
}
