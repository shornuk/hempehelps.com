<?php

use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\DiscountController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\PageController;
use App\Markets\MarketContext;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/shop', [CatalogController::class, 'index'])->name('shop.index');
Route::get('/shop/{handle}', [CatalogController::class, 'collection'])->name('shop.collection');
Route::get('/products/{handle}', [CatalogController::class, 'show'])->name('products.show');

Route::get('/collections/all', function (MarketContext $market) {
    return redirect($market->url('/shop'), 301);
})->name('collections.all');
Route::get('/collections/{handle}', function (string $handle, MarketContext $market) {
    return redirect($market->url('/shop/'.$handle), 301);
})->name('collections.redirect');

Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
Route::get('/discount/{code}', DiscountController::class)->name('discount.apply');

Route::get('/guides', [PageController::class, 'guides'])->name('guides.index');
Route::get('/guides/{slug}', [PageController::class, 'guide'])->name('guides.show');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/account', AccountController::class)
    ->middleware('auth')
    ->name('account');
