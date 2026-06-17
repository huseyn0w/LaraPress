# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Laravella CMS — a Wordpress/Joomla-style multilingual CMS built on **Laravel 8 / PHP 7.3+**. Requires the `ext-imagick` PHP extension. Front-end assets are compiled with Laravel Mix (webpack) + Bootstrap 4 + jQuery + Vue 2.

## Commands

```bash
composer install                 # PHP dependencies
npm install                      # or `yarn` — JS dependencies
cp .env.example .env             # then fill DB + API keys; php artisan key:generate
php artisan migrate --seed       # create schema AND seed roles/users/pages/posts (seeds are required for a working install)

php artisan serve                # run dev server
npm run dev                      # build assets once
npm run watch                    # rebuild assets on change
npm run prod                     # production asset build

vendor/bin/phpunit               # run the test suite
vendor/bin/phpunit --filter=SomeTest          # run a single test by name
vendor/bin/phpunit tests/Feature/ExampleTest.php   # run a single test file
```

Code style is enforced by StyleCI (`.styleci.yml`, Laravel preset) on push — there is no local lint command.

Admin panel lives at `/<APP_URL>/laravella-admin` (seeded credentials `admin` / `laravelladmin123`).

## Architecture

### Models live in a non-standard location
Eloquent models are in **`app/Http/Models/`** (not `app/Models/`). Admin-only models are under `app/Http/Models/CPanel/`. Keep this convention when adding models.

### Repository pattern
All DB access goes through repositories in `app/Repositories/`, extending the abstract `BaseRepository` (implements `BaseRepositoryInterface`). There are **two parallel families**: front-facing repos (e.g. `PostRepository`, `CategoryRepository`) and admin repos prefixed `CPanel*` (e.g. `CPanelPostRepository`). Controllers stay thin and delegate to repositories. `BaseRepository` has built-in logic for swapping to the `*Translation` model based on the current locale (`checkForTranslation`).

### Multilingual content (astrotomic/laravel-translatable)
Translatable models (`Post`, `Page`, `Category`, `Menu`) implement `TranslatableContract`, use the `Translatable` trait, and declare `$translatedAttributes`. Each has a companion `*Translation` model + `*_translations` table. The active locale is resolved per-request by the `Localization` middleware from `session('locale')`, falling back to `config('app.locale')`. Available languages are defined in `config/app.php` under `languages_list` (read via the `get_languages()` / `lang_exist()` helpers).

### Routing & access control are split front vs. admin
- **Front routes** (`routes/web.php`, default namespace): `PageController`, `PostController`, `CategoryController`, `UserController`, `PostCommentController`. Note the catch-all `/{locale?}/{slug?}` → `PageController@languageIndex` must stay last.
- **Admin routes** are all under the `laravella-admin` prefix + `CPanel` namespace, guarded by `auth` + `see_admin_panel`, with each section gated by a custom permission middleware.

The permission system is **custom** (not a package): `UserRoles` + `UserPermissions` models, and one middleware per capability (`ManageUsers`, `ManagePosts`, `ManagePages`, `ManageCategories`, `ManageComments`, `ManageRoles`, `ManageGeneralSettings`, `ManageMenu`). These are aliased in `app/Http/Kernel.php` as `manage_*`. Note: `see_admin_panel` is aliased to `ManageMenu` — verify the intended check when touching admin gating.

### Theming / template selection
Front views live under `resources/views/default/` (`pages/`, `posts/`, `categories/`, `users/`). A page's blade is chosen dynamically from a DB column: `PageController` does `view('default.pages.' . $this->data->template, ...)`. Adding a page template means adding a blade under `resources/views/default/pages/` and exposing its name to the page editor. `default` is effectively the active theme folder.

### Observers
Registered in `app/Providers/ObserverServiceProvider.php` (`PostObserver`, `PostTranslationObserver`, `PageObserver`) — they handle derived data like slug generation on model events. Add new observers there, not in `EventServiceProvider`.

### Model caching
`Post` and `Category` use the `genealabs/laravel-model-caching` `Cachable` trait. Writes auto-flush the model's cache, but be aware reads are cached when debugging stale data.

### Other conventions
- **Validation**: dedicated Form Request classes in `app/Http/Requests/`.
- **Custom helpers**: globally available, autoloaded via `bootstrap/laravella-helpers.php` (composer `files` autoload) — e.g. `get_languages()`, `lang_exist()`, `get_current_lang()`.
- **Authorization policies**: `app/Policies/UserPolicy.php` (registered in `AuthServiceProvider`).
- **Integrations**: HTML sanitization via `mews/purifier`, media via `unisharp/laravel-filemanager` (config built by `app/Handlers/LfmConfigHandler.php`), social login via `laravel/socialite` (Twitter/Facebook/LinkedIn/Google/GitHub), `albertcht/invisible-recaptcha`, image processing via `intervention/image`.
