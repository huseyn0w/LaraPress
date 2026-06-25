# Plugin / Hook Registry (P9) — Design

> Status: approved 2026-06-25. Canon: `../FEATURE_MATRIX.md` §12 (lines 142–150),
> `REFACTOR_PLAN.md` P9. Engine choice: **Laravel events** (per user decision).

## Goal

Add the extensibility layer the CMS currently lacks: **action** hooks, **filter**
hooks, and **render-region** template injection, with **in-repo plugins**
discovered from the filesystem and **enabled/disabled at runtime** via the admin
panel (no restart). Ship a bundled **reading-time** sample plugin demonstrating a
content filter. No uploaded/arbitrary code execution (preserves the project's
"no code execution" stance).

## 1. Hook engine — `App\Support\Hooks`

A singleton (bound as `hooks`) that is a thin, Laravel-idiomatic wrapper over the
event dispatcher. Hooks are **string-named Laravel events** namespaced by kind:
`hook.action.<name>`, `hook.filter.<name>`, `hook.region.<name>`.

API:
- `action(string $name, mixed ...$args): void` — dispatch a fire-and-forget hook.
- `onAction(string $name, callable $cb): void` — subscribe (wraps `Event::listen`).
- `filter(string $name, mixed $value, mixed ...$args): mixed` — dispatch a filter:
  a mutable container holding `$value` is passed to listeners by reference; each
  listener may replace `$container->value`; the final value is returned. This is
  how filters return mutated values on an event engine.
- `onFilter(string $name, callable $cb): void` — subscribe; `$cb(mixed $value, ...$args): mixed`.
- `region(string $name, array $context = []): string` — collect HTML fragments
  returned by listeners and concatenate them (in registration order).
- `onRegion(string $name, callable $cb): void` — subscribe; `$cb(array $context): string`.

Blade directive `@hook('region')` compiles to `{!! app('hooks')->region('region') !!}`.
Region output is **trusted** (in-repo, admin-toggled code), echoed raw like the
existing theme partials.

Ordering: listeners fire in registration order; plugin load order is deterministic
(alphabetical by slug). Filter **priorities are out of scope** (YAGNI).

## 2. Plugins (in-repo)

Contract `App\Plugins\Contracts\Plugin`:
```php
public function slug(): string;          // stable unique id, e.g. "reading-time"
public function name(): string;          // human label
public function description(): string;   // admin description
public function boot(Hooks $hooks): void; // register listeners here
```
Each plugin lives at `app/Plugins/<StudlyName>/<StudlyName>Plugin.php`.

## 3. Discovery + runtime enable/disable

`App\Support\PluginManager`:
- `discover(): array<Plugin>` — scan `app/Plugins/*/*Plugin.php`, instantiate
  classes implementing the `Plugin` contract. Filesystem only (no ORM).
- `sync(): void` — ensure every discovered slug has a `plugins` row; new slugs are
  inserted with `enabled = false`. (Delegates the DB write to the repository.)
- `loadEnabled(Hooks $hooks): void` — read enabled slugs (via repository),
  instantiate the matching discovered plugins, call `boot($hooks)`.
- All DB access guarded by `Schema::hasTable('plugins')` so the app boots during
  migrations / fresh installs.

`plugins` table: `id`, `slug` (unique), `enabled` (boolean, default false),
timestamps.

`App\Providers\PluginServiceProvider` (registered in `config/app.php`): in `boot()`,
resolves `Hooks` + `PluginManager`, runs `sync()` then `loadEnabled()`.

## 4. Injection points

Render regions added to the active theme: `@hook('head')` (in `<head>`),
`@hook('header')`, `@hook('footer')`. Content filter applied at
`resources/views/default/posts/post.blade.php` where the body renders:
`{!! app('hooks')->filter('the_content', $data->content) !!}`.

Minimal set by design; adding a new region/filter is a one-line `@hook`/`filter()` call.

## 5. Sample plugin — Reading-time

`app/Plugins/ReadingTime/ReadingTimePlugin.php`: registers
`onFilter('the_content', ...)`; estimates minutes from `str_word_count(strip_tags($html))`
at 200 wpm and prepends a small "N min read" badge to the post body. Demonstrates
the filter mechanism end-to-end. Seeded **disabled** by default (admin enables it).

## 6. Admin UI

Thin boundary: `CPanelPluginController` → `CPanelPluginService` (extends
`BaseCrudService`) → `CPanelPluginRepository` → `Plugin` model. Screen lists
discovered plugins (metadata from `PluginManager::discover()` merged with the
`enabled` flag from the DB) with an enable/disable toggle (POST/PUT that updates
the row; effective next request — no restart). Gated by the existing
`manage_general_settings` middleware (no new permission). en/ru lang keys.

The controller obtains the discovered metadata via the service (which calls the
manager), so the controller stays free of business logic and data access.

## 7. Layering & tests

Chain controller → service → repository → model preserved. `Hooks` and
`PluginManager` are support/service classes; the only ORM access is in
`CPanelPluginRepository`. The manager's filesystem scan is not data access.

TDD coverage:
- `Hooks`: action dispatch, filter mutation + return, region concatenation.
- `PluginManager`: discovery finds plugins, `sync()` inserts new slugs as disabled,
  `loadEnabled()` boots only enabled plugins.
- Reading-time: filter adds the badge when enabled; absent when disabled.
- `@hook` directive renders registered region output.
- Admin: list renders, toggle flips `enabled`, gate enforced.

Quality gate: suite green (shown), Pint clean, PHPStan level 5 clean (no new
baseline), then 2–3 adversarial skeptics (behavior / security / architecture).

## Out of scope (YAGNI)

Filter priorities, zip/upload of plugins, plugin versioning/dependencies, hooks in
the admin theme, per-plugin settings screens.
