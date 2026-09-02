<?php

return [
    'shop' => env('SHOPIFY_SHOP', 'hempehelps.myshopify.com'),
    'api_version' => env('SHOPIFY_API_VERSION', '2026-07'),
    'storefront_token' => env('SHOPIFY_STOREFRONT_PRIVATE_TOKEN'),
    'storefront_public_token' => env('SHOPIFY_STOREFRONT_PUBLIC_TOKEN'),
    'admin_token' => env('SHOPIFY_ADMIN_ACCESS_TOKEN'),
    'webhook_secret' => env('SHOPIFY_WEBHOOK_SECRET'),
    'webhook_replay_window' => (int) env('SHOPIFY_WEBHOOK_REPLAY_WINDOW', 300),
    'cache_ttl' => [
        'product' => (int) env('SHOPIFY_PRODUCT_CACHE_TTL', 90),
        'collection' => (int) env('SHOPIFY_COLLECTION_CACHE_TTL', 300),
        'orders' => (int) env('SHOPIFY_ORDERS_CACHE_TTL', 60),
    ],
];
