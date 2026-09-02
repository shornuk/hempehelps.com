# HEMPE Hemp House storefront

Laravel-owned website. Shopify is the commerce engine.

- Public storefront: custom Tailwind + Livewire (no Flux)
- Staff admin (`/admin`): Flux
- Auth: passwordless email link
- Reviews: Laravel-owned (one-time Judge.me import)
- Subscriptions: reserved `App\Subscriptions` — not ported in V1

## Local

```bash
composer setup
php artisan migrate --seed
php artisan shopify:poc-status
```

Herd URL: `https://new.hempehelps.test`

See `docs/spikes/PHASE-0.md` and `docs/cutover.md`. The live Dawn theme stays on `master`.
