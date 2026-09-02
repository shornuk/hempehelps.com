# Phase 0 — Spike findings (cannot run against live shop in this environment)

These are verified from current Shopify docs (API 2026-07) and the existing HEMPE theme. Re-run against `hempehelps.myshopify.com` with Headless-channel tokens before launch.

## 1. Markets / currency (PoC #1)

- PDP prices: Storefront `@inContext(country:, language:)`.
- Cart / checkout prices: `cartBuyerIdentity.countryCode`. If these disagree, PDP and checkout diverge.
- HEMPE historically displays localised prices but checkout may still charge GBP. Confirm in Shopify Markets whether checkout currency is locked to GBP. If locked, show “Charged in GBP” on the storefront — do not fake a USD checkout.
- Theme market handles already in use: `gb`, `us`, `eu`, `international`.

## 2. Collabs without Hydrogen (PoC #2)

Shopify requires for custom storefronts:

1. Shopify analytics page views
2. Honour Online Store URL redirects
3. Implement `/discount/{code}`

Official examples are Hydrogen-only. This app implements `/discount/{code}` + cart attributes (`collabs_ref` / `uppromote_ref`) + first-party referral cookie. Attribution must be tested on a real order. Theme also embeds **UpPromote** — pick one affiliate stack; do not run both blindly.

## 3. GA4 purchase

- `purchase` must fire from Shopify Customer Events / web pixel `checkout_completed`, not Laravel.
- Measure the real `checkoutUrl` host (brand domain vs `*.myshopify.com`).
- Decorate checkout with `_gl`. Exclude `myshopify.com`, Viva, checkout subdomain as referrals.
- Theme additional scripts die 26 Aug 2026 — do not rely on them.

## 4. Cart GID secrecy

Cart IDs are `gid://shopify/Cart/...?key=SECRET`. The key is a password. This app stores the full ID only in the encrypted session. Livewire views must never echo the cart GID. Covered by tests.

## 5. Admin orders after OTP

Customer Account API is Shopify-hosted OAuth — rejected. After Laravel verifies email via OTP, Admin API looks up customers/orders. Requires Protected Customer Data approval on the custom app.

## 6. Headless channel

Products must be published to the Headless sales channel. Unpublished products 404. Use Headless-channel Storefront tokens, not a theme token.

## 7. Shop subdomain

Theme CLI: `hempehelps.myshopify.com`. Subscribe app `.env.example`: `hempe-helps.myshopify.com`. Confirm before creating apps. Config default: `hempehelps.myshopify.com`.

## 8. US fulfilment

Orders still create in Shopify, so fulfilment apps should keep working. Confirm with a Headless-channel test order.

## Commands

```bash
php artisan shopify:poc-status
```
