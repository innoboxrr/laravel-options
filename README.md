# Laravel Options

**Application settings that live in the database, not in `.env`.**

Some configuration belongs to the deployment — database credentials, API keys. Some belongs to the *business* — the site name, the home page layout, the announcement banner. The second kind should not require a deploy to change.

## Options are public

The site reads options without a session, so the `index` and `show` endpoints are **public**: anyone can list every option with `GET /api/laravel-options/option/index?paginate=0`.

**Never store secrets in an option** — API keys, tokens, passwords, credentials. Those belong in `.env`.

## Install

```bash
composer require innoboxrr/laravel-options
php artisan migrate
php artisan options:seed
```

Requires PHP 8.3 and Laravel 13. The write endpoints use the `auth:sanctum` guard, so the application needs [Laravel Sanctum](https://laravel.com/docs/sanctum) (`php artisan install:api`). Exporting needs `maatwebsite/excel`.

Publishing is optional. The package publishes under the tags `config`, `views` and `vue`; those names are generic, so pass the provider too:

```bash
php artisan vendor:publish --provider="Innoboxrr\LaravelOptions\Providers\AppServiceProvider" --tag=config
```

## Reading options in PHP

```php
use Innoboxrr\LaravelOptions\Models\Option;

Option::value('site_name');            // 'Mi Sitio'
Option::value('theme');                // ['home' => [...]] — JSON objects and arrays come back decoded
Option::value('banner', 'hidden');     // the default, when the key is missing, deleted or null
```

Values are stored as text. `Option::value()` decodes JSON objects and arrays; anything else is returned as the stored string. There is no cache: each call is one query.

## HTTP API

Every route lives under `/api/laravel-options/option/`, in the `api` middleware group, named `api.laravel-options.option.*`.

| Method | URI | Name | Who can call it |
|---|---|---|---|
| GET | `index` | `index` | Anyone |
| GET | `show?option_id=` | `show` | Anyone |
| GET | `policies`, `policy` | `policies`, `policy` | Signed in |
| POST | `create` | `create` | Admins |
| PUT | `update` | `update` | Admins |
| DELETE | `delete` | `delete` | Admins (soft delete) |
| POST | `restore` | `restore` | Admins |
| DELETE | `force-delete` | `force.delete` | Nobody, until you allow it |
| POST | `export` | `export` | Admins |

`index` is filtered and paginated by [search-surge](https://github.com/innoboxrr/search-surge): **10 per page by default**. Send `paginate=0` to get every option at once, which is what a site usually wants.

`value` accepts a string, `null`, or an array/object, which is stored as JSON.

### Who is an admin

`OptionPolicy` lets through any user whose `isAdmin()` returns `true`. Every other user gets `403` on writes; a guest gets `401`. A user model without `isAdmin()` has no admins for this policy.

Permanent deletion is off even for admins. To allow it, register your own policy from the application — application providers boot after package providers, so yours wins:

```php
Gate::policy(\Innoboxrr\LaravelOptions\Models\Option::class, \App\Policies\OptionPolicy::class);
```

## Seeding defaults

```bash
php artisan options:seed
```

Creates `site_name`, `site_description` and `theme` **only if they do not exist**. Values changed from the admin panel are kept, and an option an admin deleted is not brought back. It runs without asking for confirmation, so it is safe in production and on every deploy.

## Export

`POST export` builds an `.xlsx` of the options, stores it on a disk and mails the requester a download link.

Without `maatwebsite/excel` the endpoint answers `501` with the command to install it.

## Configuration

| Key | Default | |
|---|---|---|
| `export_disk` | `env('LARAVEL_OPTIONS_EXPORT_DISK', 'local')` | Disk for exported files |
| `notification_via` | `['mail']` | Channels for the export notice. `database` needs `php artisan make:notifications-table` |
| `excel_view` | `laravel-options::excel.` | Prefix of the export view |
| `user_class` | `App\Models\User` | Not used by the package |

A config file published before 2.1 still has `export_disk => 's3'` and `notification_via => ['mail', 'database']`; change them if you do not use S3 or have no notifications table.

## Vue integration

`resources/vue/models/option.js` and a Vuex store (`--tag=vue`). Settings are usually edited from an admin panel, and shipping the model and store alongside the backend keeps the two from drifting apart.

---

Part of [Innobox R&R](https://github.com/innoboxrr) — 52 open-source packages extracted from production work. **[innobox.systems](https://innobox.systems)**
