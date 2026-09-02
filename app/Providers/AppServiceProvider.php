<?php

namespace App\Providers;

use App\Cart\CartService;
use App\Markets\MarketContext;
use App\Markets\Models\Market;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Fortify;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Fortify::ignoreRoutes();

        $this->app->scoped(MarketContext::class, fn (): MarketContext => MarketContext::default());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        View::composer('components.layouts.storefront', function ($view): void {
            $market = app(MarketContext::class);

            try {
                $cartCount = app(CartService::class)->current()->totalQuantity;
            } catch (Throwable) {
                $cartCount = 0;
            }

            $view->with([
                'market' => $market,
                'markets' => Market::query()->orderBy('key')->get(),
                'cartCount' => $cartCount,
            ]);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
