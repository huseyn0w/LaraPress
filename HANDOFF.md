# cmstack-laravel — HANDOFF

> Living handoff for the canon-convergence effort. Read this + `REFACTOR_PLAN.md` +
> `../FEATURE_MATRIX.md` + `../DESIGN_SYSTEM.md` before continuing. Last updated 2026-06-24.
>
> **Latest:** suite **238 green**, PHPStan + Pint clean. Architecture refactor complete;
> comment-notification + rate-limiting DONE; Tags taxonomy DONE; Revisions + restore UI DONE;
> Soft-delete for pages DONE; Category tree admin UI DONE; **Scheduled publishing DONE**
> (`post_translations.scheduled_at` + `posts:publish-due` command scheduled every minute; future-
> scheduled posts hidden on EVERY public read path via `Post::scopeNotScheduledForFuture`,
> status-aware so published posts stay visible; admin datetime-local schedule field) &
> adversarially verified (3 skeptics — no leaked path, no regressions). Resume at PENDING
> **RSS/Atom feeds** (P7). Optional leftovers: tags in search (§4) + admin tag-list/CRUD; revision
> storage pruning + morph map; **front never filters plain drafts (status=0, no schedule) — they're
> publicly reachable by slug (pre-existing)**; MCP post tools don't expose scheduled_at.

## Where things stand

**Branch:** `refactor/canon-convergence` (off `master`). All work committed there.
**Suite:** `php artisan test` → **238 passed (613 assertions)**, ~23s (in-memory SQLite).
**Static analysis:** `composer analyse` (PHPStan/Larastan level 5 + baseline) → **green**.
**Lint:** `composer lint` (Pint, Laravel preset) → clean on all touched files.

### Architecture map (current)

Strict layering is now enforced and verified across the whole app:

```
Controller (HTTP boundary only)
   -> Service  (business logic; NO ORM — repositories only)
       -> Repository (all Eloquent / query builder / DB lives here)
           -> Model
Service -> Event -> Listener/Observer   (for side effects of writes)
```

- **Controllers** (`app/Http/Controllers/**`): every one is a pure boundary — validate (Form
  Request) → call a service → map result to a response. **No controller calls a repository**
  (`grep -r '$this->repository' app/Http/Controllers` = none). Base controllers
  (`BaseController`, `CPanel/CPanelBaseController`) hold an untyped `protected $service`
  assigned by each subclass.
- **Services** (`app/Services/**`): `BaseCrudService` (generic CRUD over a `BaseRepository`:
  `list/getById/resolveBySlug/create/update/delete/destroy/restore`), domain services under
  `App\Services\CPanel\*` and `App\Services\Front\*`, plus `App\Services\Auth\*`. **No service
  touches the ORM** — verified by grep + 3 adversarial skeptics.
- **Repositories** (`app/Repositories/**`): unchanged two-family design (front + `CPanel*`)
  over `BaseRepository`. New methods added during the refactor: `UserRepository`
  (`findOrLinkSocialIdentity`, `createFromSocial`, `createFromRegistration`,
  `setPlainPassword`), `CPanel{Seo,Geo}SettingsRepository::saveSingleton`,
  `CPanel{Post,User,Comment}Repository` dashboard reads, `{Page,Post,Category}Repository::
  sitemapEntries`, `CategoryRepository::llmsEntries`.
- **Tooling:** `pint.json`, `phpstan.neon` (+ `phpstan-baseline.neon`, 86 frozen legacy
  findings), composer scripts `lint`/`lint:fix`/`analyse`/`test:coverage`/`check`.

## DONE (this effort)

1. **Auth service extraction** (`App\Services\Auth\SocialAuthService`,
   `UserRegistrationService`) + characterization/regression tests. Fixed a latent
   **double-hash bug** (register *and* password-reset double-hashed → login broke); now a
   single hashing path via the model mutator, with `tests/Feature/Auth/*` regression tests.
2. **Quality tooling**: Pint + Larastan(level 5 + baseline) + composer scripts.
3. **Architecture refactor (Task 2) — COMPLETE**: introduced the service layer; refactored
   **all ~22 controllers** (admin + front) to thin boundaries; moved **all** data access into
   repositories so no service touches the ORM. Event sync/async policy recorded in
   `REFACTOR_PLAN.md §1c`. Adversarially verified (layering / behavior / security+perf) —
   clean; the one finding (reset-password model mutation) was fixed.

## PENDING (ordered — resume here)

> Each item: TDD (characterization first) → suite green (show output) → 2–3 adversarial
> skeptics → fix → commit → refresh this file. Keep services repo-only and side effects in
> events/observers (with sync/async classification in `REFACTOR_PLAN.md`).

> DONE since last handoff: **Scheduled publishing** (P6) — item 6 below; +9 tests
> (suite 229 → 238), adversarially verified. Earlier this effort: Category tree admin UI (§2),
> Soft-delete for pages (§1), Revisions + restore UI (§1), comment-notification (§18/§3), Tags (§2).

1. **Tags taxonomy** (P1) — **DONE end-to-end** (schema `tags`/`tag_translations`/`post_tag`;
   `Tag`/`TagTranslation`; `Post::tags()`; `TagRepository` find-or-create+sync + `postsForTag`;
   `PostObserver::syncTags` reads the `tags` form field; admin post-form `tags` input
   (new+edit, edit pre-fills); `Front\TagViewService` + thin `TagController` + `/tag/{slug}`
   archive + view; tags-as-pills on public post detail; language switcher wired; en/ru lang
   keys). **Optional leftovers:** include tags in search (§4); a dedicated admin tag-list/CRUD.
   NB: 2 of the frozen PHPStan baseline entries are tag `relationExistence` larastan
   false-positives (identical to the category ones) — leave.
3. **Revisions + restore UI** (P2) — **DONE end-to-end** (polymorphic `revisions` table;
   `Revision` model; `RevisionRepository` snapshot/listFor/findFor/diff/restoreFrom +
   allow-list restore; `PostTranslationObserver`/`PageTranslationObserver` `updating` hook
   delegating the snapshot; `ManagesRevisions` trait on the post/page services; controller
   `revisions`/`revisionDiff`/`restoreRevision` + routes; shared `cpanel/revisions/{list,diff}`
   views + en/ru lang; transactional writes, trash/scope/authz guards; 15 tests). Adversarially
   verified. **Optional leftovers:** prune/cap `revisions.data` growth; register a morph map.
4. **Soft-delete for pages** (P3) — **DONE end-to-end** (`pages.deleted_at` migration + SoftDeletes
   on `Page`; `CPanelPageRepository` trashedPages/delete/restore/destroy; `CPanelPageService`
   trashed/runBulkAction; controller trashedPages/restore/multipleActions + routes — note the
   GET `/{id}/restore` route is registered BEFORE `/{id}/{lang}` to avoid shadowing; pages_list
   trash-tab + bulk UI + page.js destroy; en/ru lang; 11 tests). Permanent-destroy is restricted
   to already-trashed rows (`onlyTrashed`) in BOTH posts and pages. Adversarially verified.
5. **Category tree admin UI** (P4) — **DONE** (made the inert parent picker work:
   `CPanelCategoryRepository::parentOptions`/`descendantIds` build the current-locale tree and
   exclude self+descendants; form field renamed `parent_category` → `parent_category_id` so
   Astrotomic persists it to the translated column; indented dropdown + selected state;
   `CategoryRequest` cycle guard via `Rule::notIn(self+descendants)` with int normalisation;
   MCP `UpdateCategoryTool` cycle guard; 6 tests). Adversarially verified. Optional leftover:
   show the tree/parent in the category LIST view (parent picker itself satisfies the matrix).
6. **Scheduled publishing** (P6) — **DONE** (`post_translations.scheduled_at` nullable+indexed;
   `CPanelPostRepository::publishDue` + `CPanelPostService::publishDue` + `posts:publish-due`
   command scheduled `everyMinute()->withoutOverlapping()` in `App\Console\Kernel`. Front hides
   future-scheduled *drafts* via `Post::scopeNotScheduledForFuture` (status-aware) applied to ALL
   public read paths: detail (via `BaseRepository::applyFrontReadScope` hook, overridden in front
   `PostRepository`), sitemap, category/tag archives, search, home helper. Admin datetime-local
   schedule field + `ValidatePostData` `scheduled_at`; en/ru lang. 9 tests). Adversarially verified.
7. **RSS/Atom feeds** (P7, **resume here**, net-new): `/rss.xml` (+ optionally per-category) of
   PUBLISHED posts only. Build it the same way as the sitemap: a `Front\*` service + thin
   controller method + route registered BEFORE the front catch-all (see `SeoController::sitemap`
   / `SeoFeedService` for the cached-XML pattern). Reuse `PostRepository` (apply
   `notScheduledForFuture()` so scheduled posts never leak into the feed); cache like the sitemap.
8. **Membership toggle + email-verification enforcement** (P8): wire the dangling settings.
9. **Plugin/hook registry** (P9): adopt django's action/filter/render-region model (largest).
10. **Coverage → ≥80% on services/repos + 100% critical paths**, and **CI pipeline**
    (lint → analyse → test → build → e2e). NOTE: this box has **no Xdebug/PCOV** installed,
    so `--coverage` cannot run here yet — install PCOV (or run in CI) before reporting numbers.
11. **UI redesign to `../DESIGN_SYSTEM.md`** (Task 3, biggest): tokens → self-hosted fonts
    (Newsreader/Inter/Geist Mono) → Blade components → perf budget → a11y. **Lighthouse ≥95
    mobile must be MEASURED** with a real run (needs served app + headless Chrome against
    MySQL — not available in this sandbox; flag the env need, never assert the score).
12. **README rewrite** (Task 5) + **completeness-critic** pass.

## Key decisions / rejected options

- Service-per-domain extending `BaseCrudService`, injecting the **concrete repository** as a
  private prop (`private CPanelPostRepository $repo`) and passing it to `parent::__construct`
  so generic CRUD is type-safe while domain methods call repo-specific methods. Rejected:
  adding repo methods to the interface (forces all repos to implement); a second source of
  truth.
- `protected $service` left **untyped** on base controllers (matches the legacy untyped
  `$repository`) because PHP forbids covariant narrowing of an inherited typed property and
  subclasses call domain-specific methods on it. PHPStan stays green (mixed allows the calls);
  the architecture rule is about layering, not type hints.
- No Strategy/Factory/Adapter introduced — would be over-engineering here (scope guardrail).
- `ContactService::send()` sends mail directly (exempt from the events rule): the contact
  form's mail IS the primary action, not a side effect of a DB write.

## How to run

```bash
# in cmstack-laravel/
php artisan test                       # full suite (in-memory SQLite; never touches MySQL)
composer lint                          # pint --test
composer analyse                       # phpstan level 5 (+ baseline)
composer check                         # lint + analyse + test
# App (needs MySQL 8 + ext-imagick): make setup ; admin at /cmstack-laravel-admin
```

## Gotchas

- **Models live in `app/Http/Models/`** (not `app/Models/`); admin models under `CPanel/`.
- **Test isolation** is pinned in `tests/CreatesApplication.php` (forces SQLite `:memory:`);
  don't weaken it — Docker injects `DB_CONNECTION=mysql` and would wipe the dev DB.
- **Password hashing**: `User::setPasswordAttribute` hashes on assign. NEVER `Hash::make()`
  before assigning — that double-hashes (the bug fixed this effort). Pass plaintext.
- **PHPStan baseline**: if a refactor removes a previously-frozen error you'll get
  `ignore.unmatched`; regenerate with `phpstan analyse --generate-baseline=phpstan-baseline.neon`.
  Never baseline a NEW error in your own code — fix it.
- **Pint** auto-removes unused imports and FQCN-collapses docblock types; re-run it after
  edits and let it re-add a `use` for a `@param`/`@return` class.
- No Xdebug/PCOV here → coverage numbers require CI or a local PCOV install.
- **Admin route ordering**: the content groups have a greedy `GET /{id}/{lang}` editor route
  whose `{lang}` is unconstrained. Any other 2-segment `GET /{id}/<literal>` route (e.g.
  `/{id}/restore`) MUST be registered BEFORE it, or it is shadowed (matched as edit with
  `lang="<literal>"`). Posts/pages restore routes are placed accordingly.

---

## READY-TO-PASTE CONTINUATION PROMPT (new window)

```
You are a senior Laravel/PHP engineer continuing the cmstack-laravel canon-convergence
work AUTONOMOUSLY in /Users/huseyn0w/Desktop/SWE/cmstack/cmstack-laravel (git branch
refactor/canon-convergence).

First read, in order: cmstack-laravel/HANDOFF.md, cmstack-laravel/REFACTOR_PLAN.md,
../FEATURE_MATRIX.md, ../DESIGN_SYSTEM.md (the last two are read-only canon — do NOT edit;
if either is missing, stop and tell me in Russian "Нет общих спеков — сначала запусти
prompts/00-bootstrap.md"). Then resume from the FIRST item in HANDOFF.md "PENDING".

Operating rules (unchanged):
- Work autonomously inside cmstack-laravel/; don't ask permission for reads/edits/artisan/
  composer/npm/tests/local git. Only stop for genuinely irreversible actions or a product
  decision the spec files don't answer (batch such questions).
- HARD RULES (top priority, non-negotiable): controllers in app/Http/Controllers contain
  ZERO business logic and ZERO data access — pure HTTP boundary (validate via Form Request →
  call a SERVICE → map result to response). Services access data ONLY through repositories
  (no Eloquent/query-builder/DB/raw SQL in services). Side effects of writes go through
  domain events → listeners/observers, each classified synchronous (in-transaction) or
  asynchronous (queued) and recorded in REFACTOR_PLAN.md. Chain: controller → service →
  repository → model.
- Use Superpowers skills in order: brainstorming (only if scope unclear), writing-plans,
  test-driven-development, subagent-driven-development, requesting-code-review,
  verification-before-completion. Follow rigid skills exactly.
- Max-quality subagent routing; for every refactor/feature dispatch 2–3 INDEPENDENT
  adversarial Opus skeptics (behavior / correctness / security / performance) that try to
  REFUTE; treat a finding resolved only when a majority cannot break it.
- Keep the suite green and SHOW real output; never claim passing without the run. Targets:
  ≥80% coverage on services/repos + 100% of critical paths (needs PCOV/CI — no coverage
  driver in the sandbox, flag it). Lint (Pint) + static analysis (Larastan level 5) stay
  clean; new code adds no baseline entries.
- Respond to me in RUSSIAN; all code/comments/identifiers/commit messages/.md docs in
  English. Commit each verified slice (Co-Authored-By: Claude Opus 4.8
  <noreply@anthropic.com>). When context drops below ~50%, refresh HANDOFF.md (incl. this
  continuation prompt) and tell me in Russian to open a new window.

Start with PENDING **RSS/Atom feeds** (P7). Already DONE this effort: architecture refactor,
comment-notification + rate-limiting (§18/§3), Tags taxonomy (§2), Revisions + restore UI (§1),
Soft-delete for pages (§1, P3), Category tree admin UI (§2, P4), and Scheduled publishing (§1, P6).
```
