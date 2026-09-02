<?php

use App\Http\Controllers\Auth\VerifyMagicLinkController;
use App\Http\Controllers\Seo\RobotsController;
use App\Http\Controllers\Seo\SitemapController;
use App\Http\Controllers\Webhooks\ShopifyWebhookController;
use App\Livewire\Actions\Logout;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Pages\Index as AdminPages;
use App\Livewire\Admin\Products\Index as AdminProducts;
use App\Livewire\Admin\Redirects\Index as AdminRedirects;
use App\Livewire\Admin\Reviews\Form as AdminReviewForm;
use App\Livewire\Admin\Reviews\Index as AdminReviews;
use App\Livewire\Auth\OtpLogin;
use Illuminate\Support\Facades\Route;

$storefront = base_path('routes/storefront.php');

require $storefront;

Route::prefix('{marketPrefix}')
    ->name('market.')
    ->whereIn('marketPrefix', array_keys(config('markets.prefixes', [])))
    ->group($storefront);

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::middleware('guest')->group(function () {
    Route::get('/login', OtpLogin::class)->name('login');
    Route::get('/login/verify', VerifyMagicLinkController::class)->name('login.verify');
});

Route::post('/logout', Logout::class)->middleware('auth')->name('logout');

Route::redirect('/dashboard', '/admin')->name('dashboard');

Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/reviews', AdminReviews::class)->name('reviews.index');
    Route::get('/reviews/create', AdminReviewForm::class)->name('reviews.create');
    Route::get('/reviews/{review}/edit', AdminReviewForm::class)->name('reviews.edit');
    Route::get('/pages', AdminPages::class)->name('pages.index');
    Route::get('/redirects', AdminRedirects::class)->name('redirects.index');
    Route::get('/products', AdminProducts::class)->name('products.index');
});

Route::post('/webhooks/shopify/{topic}', ShopifyWebhookController::class)
    ->where('topic', '.*')
    ->name('webhooks.shopify');

require __DIR__.'/settings.php';
