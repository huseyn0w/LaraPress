# Laravella CMS
<p align="center">
<img alt="Laravella Logo" src="https://raw.githubusercontent.com/huseyn0w/Laravella-CMS/master/public/front/default/img/readme.png">
</p>
The CMS Project based on Laravel PHP Framework

## Getting Started
Laravella CMS is developed as analog to such popular CMS as Wordpress, DLE, Joomla and etc, but based on framework Laravel.
These instructions will get you a copy of the project up and running on your local machine / server for using or testing it.
See deployment for notes on how to deploy the project on a live system.

## Requirements

* **PHP 8.2+** (developed and tested on PHP 8.3) with the `imagick` extension
* **Composer 2**
* **MySQL 8** (production/local) — SQLite is used only for the test suite
* **Node.js 20+** and npm (for the Vite/Tailwind asset build)

## Tech stack

* **Laravel 11** (PHP framework)
* **Tailwind CSS 3** + **Alpine.js**, bundled with **Vite** — lightweight front-end, no jQuery/plugin bloat
* **MySQL 8** with `astrotomic/laravel-translatable` for multilingual content
* Repository pattern, custom role/permission middleware, model caching
* Google reCAPTCHA (v3) for spam protection — gracefully disabled when no keys are configured
* Built-in **SEO/GEO**: Open Graph, Twitter cards, canonical + `hreflang`, JSON-LD structured data, `sitemap.xml`, `robots.txt`, `llms.txt`
* **152 automated tests** (PHPUnit) running on isolated in-memory SQLite

## Installation (manual / no Docker)

> Prefer containers? Skip to **[Run locally with Docker](#run-locally-with-docker-development-only)** below.

1. Clone the project.
2. Copy the env file and fill in your settings (database, mail, API keys, default language):
   ```bash
   cp .env.example .env
   ```
3. Install PHP and front-end dependencies:
   ```bash
   composer install
   npm install
   ```
4. Generate the app key and build assets:
   ```bash
   php artisan key:generate
   npm run build            # or `npm run dev` for the Vite dev server with HMR
   ```
5. Run the database migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
6. Serve the app:
   ```bash
   php artisan serve
   ```

### Spam protection (reCAPTCHA)

The contact, search, and password-change forms support Google reCAPTCHA v3. It is
**disabled by default** (forms work without it). To enable it, set in `.env`:

```bash
CAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key
```

## SEO, GEO & performance

The CMS ships SEO/GEO support out of the box, built to stay fast (no WordPress-style
script bloat — the public pages load only the Vite bundle and, optionally, a single
async analytics tag):

* Per-page `<title>` / `<meta description>`, `<link rel="canonical">`, and per-entity
  `noindex` / canonical overrides.
* **Open Graph** + **Twitter Card** tags (with a configurable default social image).
* **`hreflang`** alternates (en/ru + `x-default`) for the multilingual content.
* **JSON-LD structured data** (schema.org) — `WebSite`+`SearchAction`+`Organization`
  on the homepage, `BlogPosting`+`BreadcrumbList` on posts, `CollectionPage` on
  categories, `ProfilePage`/`Person` on profiles — which also helps generative engines (GEO).
* Dynamic **`/sitemap.xml`** (pages/posts/categories with `hreflang`), **`/robots.txt`**,
  and **`/llms.txt`**.
* Lazy-loaded images with width/height (CLS-safe), `preconnect` fonts with `display=swap`,
  deferred/module scripts.

Configure global SEO defaults in the admin panel under **Settings → SEO**
(title separator, default description, default OG image, social handles, Google/Bing
verification tags, optional GA4/GTM id loaded async, a global "discourage search engines"
toggle, sitemap toggle, and extra `robots.txt` lines).

## Testing

```bash
php artisan test                          # full suite (host)
docker compose exec app php artisan test  # inside Docker
php artisan test --filter=SeoMetaTest     # a single test
```

Tests run on an **isolated in-memory SQLite** database (pinned in `tests/CreatesApplication.php`)
so they never touch your local MySQL/Docker data.

## Administrator area credentials:
Go to: SITE_URL/laravella-admin
<pre>
Username: admin
Password: laravelladmin123
</pre>

## To manage website languages:
1) Open config/app.php file and edit array of languages:
<pre>
'languages_list' => [
    'en'  => ['title' => 'English', 'icon' => env('APP_URL').'/admin/img/flags/en.png'],
    'ru'  => ['title' => 'Русский', 'icon' => env('APP_URL').'/admin/img/flags/ru.png']
]
</pre>

2) Open resourses/lang/ folder to manage language localization string files.


## General features
* Full CMS: pages, posts, categories, menus, comments, media/file manager
* Modern responsive UI (Tailwind CSS) for both the public site and the admin panel
* Built-in SEO/GEO (Open Graph, JSON-LD, sitemap/robots, hreflang) — see above
* Social-media authentication (Twitter, Facebook, LinkedIn, Google, GitHub)
* Website search
* Users, roles & granular permissions
* Custom fields
* Flexible template-changing system
* Multiple languages (multilingual content via translations)
* Database caching

## Planning additional features
* E-Commerce extension
* REST API for posts and pages



## Advantages for developers
Why it is easy to extend? Because it was written by using best practices and technologies, such as:
* Short controllers
* Separate Validator Request classes
* Repository pattern to work with DB
* Middlewares
* Observers
* Policies
* Beautiful text editor (TinyMCE)
* Perfect File Manager (Laravel FileManager)

## Version

Current Version of Laravel Framework is **11.x**

Current version of CMS is **2.0** (modernized: Laravel 11 / PHP 8.3, Vite + Tailwind, Dockerized local dev)

## Author

* **Elman Hüseynov** - [huseyn0w](https://linkedin.com/in/huseyn0w)

## Contributor

* **Ilkin Alibayli** - [ilkinalibayli](https://www.linkedin.com/in/ilkin-alibayli/)

## License

This project is licensed under the Public V3 License - see the [LICENSE.md](LICENSE.md) file for details

<!-- ===================================================================== -->
<!-- Phase 2.5: Local Docker dev + Hostinger (no-Docker) deployment        -->
<!-- ===================================================================== -->

## Run locally with Docker (development only)

> The Docker setup is a **local development convenience only**. It is **not**
> used in production and the application has **no runtime dependency on Docker**.
> See *Deploy to Hostinger* below for the production flow.

**Requirements:** Docker + Docker Compose.

1. Copy the env file and point the DB at the Docker MySQL service:
   ```bash
   cp .env.example .env
   # In .env set:
   #   DB_HOST=mysql
   #   DB_CONNECTION=mysql
   ```
2. Build and start the stack:
   ```bash
   docker compose up -d
   ```
   Services started: `app` (PHP 8.3-FPM with imagick), `web` (nginx on
   http://localhost:8080), `mysql` (MySQL 8, published on host port 33060).
3. Install PHP dependencies, generate the key, and migrate/seed:
   ```bash
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   ```
4. Build front-end assets. Either run Vite once on the host (or in the node
   service) for a static build, or use the HMR dev server:
   ```bash
   # One-off build (host):
   npm install && npm run build

   # OR live HMR via the optional node service:
   docker compose --profile dev up node   # Vite on http://localhost:5173
   ```
5. Open the app: **http://localhost:8080**
   Admin area: http://localhost:8080/laravella-admin (see credentials above).

**Optional services**

- **Mailpit** (catches local outgoing mail, UI on http://localhost:8025):
  ```bash
  docker compose --profile mail up -d mailpit
  ```
  Then in `.env`: `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025`.
- **node / Vite** is also optional (see step 4). For a non-HMR workflow just
  `npm run build` once and skip it.

**Tests** run against SQLite in-memory (configured in `phpunit.xml`), so no DB
is required:
```bash
docker compose exec app php artisan test   # or simply `php artisan test` on the host
```

The Docker-specific files are: `Dockerfile`, `docker-compose.yml`,
`docker/nginx/default.conf`, `docker/php/php.ini`, `.dockerignore`. **None of
these are deployed to or used by production.**

## Deploy to Hostinger (no Docker)

Production runs on **traditional Hostinger PHP-FPM hosting — Docker is never
used here.** The Docker files above are development-only.

1. Upload / pull the code to your Hostinger account.
2. Install PHP dependencies (production, optimized):
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
3. Build front-end assets and upload the result. Build locally (or in CI) and
   commit/upload the generated `public/build/` directory:
   ```bash
   npm install && npm run build
   ```
4. Set the domain's **document root to `/public`** in hPanel.
5. Configure `.env` with your Hostinger MySQL credentials:
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=localhost          # Hostinger MySQL is local to the account
   DB_PORT=3306
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   APP_ENV=production
   APP_DEBUG=false
   ```
6. Generate the app key (once) and run migrations, then cache config/routes
   via Hostinger SSH (or a one-off cron job):
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   ```
7. Ensure the server has the **`imagick` PHP extension** enabled (required by
   the file manager / image features). On Hostinger this is selectable in the
   PHP extensions panel.

> The app must run identically from a plain `composer install --no-dev` plus
> built assets on Hostinger's PHP-FPM. **No Docker, nginx config, or `docker/`
> file is part of the production runtime.**

