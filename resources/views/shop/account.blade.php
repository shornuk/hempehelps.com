<x-layouts.storefront title="Account">
    <div class="mx-auto max-w-3xl px-4 py-16">
        <h1 class="text-4xl font-semibold">Hello, {{ $user->name }}</h1>
        <p class="mt-2 text-sm text-muted">{{ $user->email }}</p>

        <h2 class="mt-10 text-2xl font-semibold">Orders</h2>
        @if (! $user->email_verified_at)
            <p class="mt-4 text-sm text-muted">Verify your email to load Shopify order history.</p>
        @elseif ($orders === [])
            <p class="mt-4 text-sm text-muted">No Shopify orders found for this email yet.</p>
        @else
            <ul class="mt-6 divide-y divide-black/10">
                @foreach ($orders as $order)
                    <li class="py-4">
                        <div class="flex items-center justify-between">
                            <strong>{{ $order['name'] ?? $order['id'] ?? 'Order' }}</strong>
                            <span class="text-sm text-muted">{{ $order['financialStatus'] ?? '' }}</span>
                        </div>
                        @if (! empty($order['statusPageUrl']))
                            <a href="{{ $order['statusPageUrl'] }}" class="text-sm text-hempe">View order status</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="mt-10">
            @csrf
            <button class="text-sm text-muted underline">Sign out</button>
        </form>
    </div>
</x-layouts.storefront>
