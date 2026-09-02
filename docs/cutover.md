# Staging and DNS cutover

V1 ships the Laravel storefront in parallel with the live Dawn theme.

## Staging

- Host: `staging.hempehelps.com` (or Herd/Cloud equivalent)
- MySQL + Redis
- Headless channel tokens (same shop is fine if webhook URL is isolated)
- Confirm `APP_ENV` cannot `ordersCreate` except the later subscription job

## Cutover checklist

1. Publish catalogue to the Headless sales channel
2. Point `hempehelps.com` + market hosts at Laravel
3. Set Shopify continue-shopping / checkout domain to Laravel hosts
4. Confirm 301 table covers `/collections/*`, `/account/login`, `/blogs/guides`
5. Enable GA4 linker across Laravel hosts + real checkout host
6. Re-test Collabs `/discount/{code}`
7. Password the Online Store theme; keep Admin/fulfilment/Viva on Shopify

See `docs/spikes/PHASE-0.md` for shop-side PoCs that must pass first.
