@props(['title' => null, 'description' => null, 'canonical' => null, 'schema' => null])

@php
    $market = $market ?? app(\App\Markets\MarketContext::class);
    $markets = $markets ?? collect();
    $cartCount = $cartCount ?? 0;
    $pageTitle = $title ? $title.' · HEMPE' : 'HEMPE';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $market->locale) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle }}</title>
        @if ($description)
            <meta name="description" content="{{ $description }}">
        @endif
        <link rel="canonical" href="{{ $canonical ?? url()->current() }}">
        @foreach ($markets as $hreflang)
            <link rel="alternate" hreflang="{{ $hreflang->locale }}" href="{{ url(($hreflang->path_prefix ? '/'.$hreflang->path_prefix : '').request()->getPathInfo()) }}">
        @endforeach
        <link rel="icon" href="/favicon.ico" sizes="any">
        @fonts
        @vite(['resources/css/storefront.css', 'resources/js/app.js'])
        @if (config('analytics.ga4_id'))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('analytics.ga4_id') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', @json(config('analytics.ga4_id')), { linker: { domains: @json(config('analytics.checkout_hosts')) } });
            </script>
        @endif
        @if ($schema)
            <script type="application/ld+json">{!! json_encode($schema) !!}</script>
        @endif
    </head>
    <body class="min-h-screen bg-paper text-ink antialiased">
        <livewire:shop.market-banner />

        <header class="border-b border-black/10 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-4">
                <a href="{{ $market->url('/') }}" class="text-xl font-semibold tracking-tight text-hempe">HEMPE</a>
                <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                    <a href="{{ $market->url('/shop') }}">Shop</a>
                    <a href="{{ $market->url('/guides') }}">Guides</a>
                    <a href="{{ $market->url('/faq') }}">FAQ</a>
                    <a href="{{ $market->url('/pages/our-story') }}">Our story</a>
                </nav>
                <div class="flex items-center gap-4 text-sm">
                    @auth
                        <a href="{{ route('account') }}">Account</a>
                    @else
                        <a href="{{ route('login') }}">Sign in</a>
                    @endauth
                    <button type="button" class="rounded-full bg-hempe px-3 py-1.5 text-white" onclick="Livewire.dispatch('open-cart')">
                        Cart ({{ $cartCount }})
                    </button>
                </div>
            </div>
        </header>

        @if ($market->chargeInGbp && $market->currency !== 'GBP')
            <p class="bg-hempe-light px-4 py-2 text-center text-sm text-hempe-dark">
                Prices shown in {{ $market->currency }}. Checkout may charge in GBP.
            </p>
        @endif

        <main>
            {{ $slot }}
        </main>

        <footer class="mt-16 border-t border-black/10 bg-white">
            <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 text-sm text-muted md:flex-row md:justify-between">
                <p>© {{ date('Y') }} HEMPE. HEMPEhelps.</p>
                <div class="flex gap-4">
                    <a href="{{ $market->url('/pages/contact') }}">Contact</a>
                    <a href="{{ $market->url('/faq') }}">FAQ</a>
                    <a href="{{ route('account') }}">Orders</a>
                </div>
            </div>
        </footer>

        <livewire:shop.cart-drawer />
    </body>
</html>
