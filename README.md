# Laravel Options

**Application settings that live in the database, not in `.env`.**

Some configuration belongs to the deployment — database credentials, API keys. Some belongs to the *business* — the invoice footer, the support email, whether signups are open this week. The second kind should not require a deploy to change.

```php
Option::set('billing.invoice_footer', 'Gracias por su preferencia');
Option::get('signups.open', true);
```

## What ships

| | |
|---|---|
| `Option` model | Key-value with typed casting and caching |
| API resource | Read and update options over HTTP, authorisation-gated |
| Seeder + command | Define defaults in code, seed them idempotently |
| Excel export | `OptionsExports` — dump the current configuration for review |
| Vue integration | `resources/vue/models/option.js` and a ready Vuex store |

**The Vue side is included on purpose.** Settings are usually edited from an admin panel, and shipping the model and store alongside the backend means the two cannot drift apart.

## Seeding defaults

```bash
php artisan option:seed
```

Idempotent — new options are inserted, existing values are left alone. Safe to run on every deploy.

## Install

```bash
composer require innoboxrr/laravel-options
php artisan vendor:publish --tag=laravel-options-config
php artisan migrate
```

---

Part of [Innobox R&R](https://github.com/innoboxrr) — 52 open-source packages extracted from production work. **[innobox.systems](https://innobox.systems)**
