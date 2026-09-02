<?php

return [
    'ga4_id' => env('GA4_MEASUREMENT_ID'),
    'checkout_hosts' => array_values(array_filter(array_map(
        trim(...),
        explode(',', (string) env('GA4_CHECKOUT_HOSTS', 'hempehelps.myshopify.com,checkout.shopify.com')),
    ))),
];
