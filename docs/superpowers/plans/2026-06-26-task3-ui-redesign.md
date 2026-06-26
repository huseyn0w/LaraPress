# Task 3 — UI Redesign to DESIGN_SYSTEM.md Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Bring the Laravel/Blade product to a *pixel-faithful* implementation of `../DESIGN_SYSTEM.md` — a CSS-custom-property token contract (light + dark), self-hosted variable fonts, a real Blade component library, and the editorial "quiet-luxury" treatment applied across every public page and the admin panel, meeting the §7 performance and §8 a11y rules.

**Architecture:** The canonical contract is **tokens as CSS custom properties** (`:root` light, `.dark` dark) with the exact §2 hex; Tailwind's `theme.extend` *bridges* utilities to those vars (`bg-surface`→`var(--surface)`, `text-muted`→`var(--text-muted)`, etc.) so existing utility usage keeps working while gaining dark-mode + theme re-scoping for free. Components become **Blade `<x-*>` components** (today there are zero) so the spec's states/a11y live in one place. Public pages stay near-zero-JS (Alpine islands only); admin keeps its Alpine shell + toast/modal/collapse runtime.

**Tech Stack:** Laravel 11 / Blade, Tailwind 3.4 + Vite 5, Alpine 3, `@fontsource-variable/{newsreader,inter,geist-mono}` (self-hosted woff2), no font/icon CDN.

## Global Constraints

- **The design system is canon and prescriptive.** `../DESIGN_SYSTEM.md` gives exactly one value for every choice — implement it verbatim; never substitute. Where it conflicts with the *current* code, the spec wins:
  - Sans UI font is **Inter** (current code uses "Inter Tight" — switch to Inter).
  - Fonts are **self-hosted** woff2, `preload`ed, subset, `font-display: swap`, **no font CDN** (remove the Google Fonts `<link>` in `header.blade.php:49-51` and the Font Awesome MaxCDN `<link>` in `cpanel/core/header-styles.blade.php`). Total font weight ≤ 120KB.
  - Add the **`--*` token layer** (§2) as the single source of truth; Tailwind colors reference the vars, not raw hex.
  - Dark mode exists via a `.dark` class (admin gets a toggle; public site light by default unless a later decision adds a toggle).
- **Branch:** `refactor/canon-convergence`. Commit each verified slice; plain message, **NO `Co-Authored-By` / Claude attribution trailer**.
- **Keep the test suite green** (`./vendor/bin/pest`, currently **322 passed**) and SHOW output after every slice. The HTTP feature tests assert markup/routes — they must keep passing as views change. The Pest browser suite (`tests/Browser/`, CI-only) asserts *computed styles*; update its `data-testid` hooks + assertions to the new design as part of the relevant slices.
- **Preflight stays disabled globally** (`tailwind.config.js corePlugins.preflight: false`); the public reset is scoped under `.theme-default`, admin under `.theme-admin`. Do not enable global preflight (it would clobber the other theme). Dark tokens flip under `.dark` *within* each theme scope.
- **Performance budget (§7) is partly UN-MEASURABLE in this sandbox.** Lighthouse ≥95 (mobile, Perf/SEO/A11y/Best-Practices), LCP/CLS/INP, and the route JS/CSS budgets need a served app + headless Chrome (and MySQL) — NOT available here. Author to the budget (subset fonts, lazy images, srcset, near-zero JS) and **flag** that the numbers are measured by Lighthouse CI / a real run, never asserted unrun.
- **A11y is mandatory per component** (§5/§8): semantic landmarks, one h1, sequential headings, visible 2px `--ring` focus, focus-trapped modals/drawers with `Esc`, skip-to-content link, programmatic labels, `prefers-reduced-motion` disables motion. Every screen verifies against §8 before "done".
- **Models/architecture untouched** — this is a view/CSS/JS-asset effort only. No controller/service/repository changes except adding view-data a template genuinely needs (route a request through a service if so; never query in a Blade).

---

## File Structure

| Path | Responsibility | Phase |
|------|----------------|-------|
| `resources/css/tokens.css` (new) | `:root` (light) + `.dark` §2 color tokens, §4 radius/spacing, §6 motion vars — imported first by both bundles | 1 |
| `tailwind.config.js` | Bridge `theme.extend.colors`/radius/shadow/timing to the CSS vars; `darkMode: 'class'`; `fontFamily` Newsreader/Inter/Geist Mono; §3 fluid type scale | 1 |
| `resources/css/app.css` | Front `.theme-default` reset + base typography consuming tokens; trim component classes superseded by Blade components | 1,3,4 |
| `resources/css/admin.css` | Admin `.theme-admin` reset + base consuming tokens; dark variants | 1,6 |
| `package.json` | `@fontsource-variable/{newsreader,inter,geist-mono}`; remove nothing else | 2 |
| `resources/css/fonts.css` (new) | `@font-face`/import of the variable woff2 + subset; imported by both bundles | 2 |
| `resources/views/default/header.blade.php` | Remove Google Fonts link; add `<link rel=preload>` for 2 critical weights; rebuild sticky header per §5 | 2,4 |
| `resources/views/components/**` (new) | Blade component library: `button`, `badge`, `card`, `alert`, `breadcrumb`, `pagination`, `avatar`, `dropdown`, `modal`, `tabs`, `empty-state`, `field` (label+control+helper/error), `toast-region`, `eyebrow`, `icon` | 3 |
| `resources/views/default/{header,footer}.blade.php` + `partials/*` | Public shell per §5 (header/footer/breadcrumb) | 4 |
| `resources/views/default/{pages,posts,categories,tags,users}/**` + `resources/views/auth/**` | Apply components to every public page; port the 4 Bootstrap-4 auth views | 5 |
| `resources/views/cpanel/core/*`, `cpanel/nav/*` | Admin shell: sidebar (§5), topbar (§5) + dark/light toggle; remove FA CDN | 6 |
| `resources/views/cpanel/**` (30 section views) | Apply admin components (tables+bulk bar, tabs, forms, modals, badges) | 7 |
| `resources/js/front.js`, `admin.js` | Wire focus-trap (drawer/modal), dark-mode toggle (admin), keep islands | 4,6 |
| `tests/Browser/*` | Update `data-testid` + computed-style assertions to the new design | 4,5,6 |

Each phase is an independently shippable, separately committed slice; execute in order (2 needs 1; 3 needs 1+2; 4–5 need 3; 6–7 need 1–3).

---

## Phase 1 — Token foundation (CSS custom properties + Tailwind bridge)

**Files:** Create `resources/css/tokens.css`; modify `tailwind.config.js`, `resources/css/app.css`, `resources/css/admin.css`.

**Interfaces — Produces:** a `--token` layer every later phase consumes; Tailwind utilities (`bg-bg`, `bg-surface`, `bg-surface-2`, `text` , `text-muted`, `text-subtle`, `border-border`, `border-strong`, `bg-primary`, `text-primary`, `ring-ring`, `bg-success-bg`/`text-success`, etc.) that resolve to the vars; `darkMode: 'class'`.

- [ ] **Step 1: Create `resources/css/tokens.css`** with the EXACT §2 values:

```css
/* Canonical design tokens — DESIGN_SYSTEM.md §2/§4/§6. Single source of truth. */
:root {
  --bg:#FBFAF7; --surface:#FFFFFF; --surface-2:#F4F1EA;
  --text:#1A1A18; --text-muted:#6B6760; --text-subtle:#94908A;
  --primary:#B23A2E; --primary-hover:#992F25; --primary-contrast:#FFFFFF;
  --accent:#C2683C; --border:#E6E1D7; --border-strong:#D2CCBE; --ring:#B23A2E;
  --success:#2F7D5B; --success-bg:#E7F1EA; --warning:#A9701A; --warning-bg:#F6EEDD;
  --error:#B4271F; --error-bg:#F7E7E5;
  /* radius §4 */
  --radius-sm:6px; --radius-md:10px; --radius-lg:16px; --radius-xl:24px; --radius-full:9999px;
  /* spacing §4 */
  --space-1:4px; --space-2:8px; --space-3:12px; --space-4:16px; --space-5:24px;
  --space-6:32px; --space-7:48px; --space-8:64px; --space-9:96px; --space-10:128px;
  /* motion §6 */
  --ease-out:cubic-bezier(0.16,1,0.3,1); --ease-in-out:cubic-bezier(0.65,0,0.35,1);
  --dur-fast:120ms; --dur-base:200ms; --dur-slow:320ms;
  /* containers §4 */
  --container-prose:720px; --container-content:1080px; --container-wide:1280px;
}
.dark {
  --bg:#0F0F10; --surface:#17171A; --surface-2:#202024;
  --text:#F4F2EC; --text-muted:#A7A29A; --text-subtle:#6E6A63;
  --primary:#E0795F; --primary-hover:#EC8B72; --primary-contrast:#1A1110;
  --accent:#E0A06A; --border:#2A2A2E; --border-strong:#3A3A40; --ring:#E0795F;
  --success:#5FB98C; --success-bg:#16271F; --warning:#D6A24C; --warning-bg:#2A2113;
  --error:#E8675C; --error-bg:#2A1513;
}
```

- [ ] **Step 2: Bridge Tailwind to the vars** in `tailwind.config.js`. Set `darkMode: 'class'`. Replace the hardcoded `colors` block so semantic utilities map to vars (keep the legacy `brand`/`ink` ramps temporarily for back-compat during migration, but add):

```js
colors: {
  bg: 'var(--bg)', surface: 'var(--surface)', 'surface-2': 'var(--surface-2)',
  text: { DEFAULT:'var(--text)', muted:'var(--text-muted)', subtle:'var(--text-subtle)' },
  primary: { DEFAULT:'var(--primary)', hover:'var(--primary-hover)', contrast:'var(--primary-contrast)' },
  accent: 'var(--accent)',
  border: { DEFAULT:'var(--border)', strong:'var(--border-strong)' },
  ring: 'var(--ring)',
  success: { DEFAULT:'var(--success)', bg:'var(--success-bg)' },
  warning: { DEFAULT:'var(--warning)', bg:'var(--warning-bg)' },
  error: { DEFAULT:'var(--error)', bg:'var(--error-bg)' },
  // keep brand/ink/paper/danger/info ramps for now (removed when no view references them)
},
borderRadius: { sm:'var(--radius-sm)', md:'var(--radius-md)', lg:'var(--radius-lg)', xl:'var(--radius-xl)', full:'var(--radius-full)' },
transitionTimingFunction: { 'out-expo':'var(--ease-out)', 'in-out-expo':'var(--ease-in-out)' },
```
> Note: mapping `borderRadius` keys to vars changes what `rounded-lg` etc. resolve to. Audit existing `rounded-*` usage so the visual result still matches §4 (default card = `--radius-lg` 16px, buttons = `--radius-md` 10px). Adjust per-component in later phases, not globally here.

- [ ] **Step 3: Import tokens first** in both bundles. Top of `resources/css/app.css` and `resources/css/admin.css`, before `@tailwind base`:
```css
@import './tokens.css';
```

- [ ] **Step 4: Re-point the scoped resets to tokens.** In `app.css` `.theme-default` base: body `background:var(--bg); color:var(--text)`; headings `color:var(--text)`; focus-visible `outline:2px solid var(--ring)`. In `admin.css` `.theme-admin`: body `background:var(--bg)` (or `--surface-2` per §ical), text vars. Replace `theme('colors.ink...')`/`theme('colors.brand...')` in the base layers with the var-backed utilities.

- [ ] **Step 5: Build + suite green.**
Run: `npm run build 2>&1 | tail -5 && ./vendor/bin/pest 2>&1 | tail -5`
Expected: Vite build succeeds (manifest written); Pest **322 passed**. No visual class is undefined.

- [ ] **Step 6: Commit.**
```bash
git add resources/css/tokens.css resources/css/app.css resources/css/admin.css tailwind.config.js
git commit -m "design: add CSS custom-property token layer (DESIGN_SYSTEM §2) + Tailwind bridge + dark mode"
```

---

## Phase 2 — Self-hosted fonts (remove all font CDNs)

**Files:** `package.json` (+lock), new `resources/css/fonts.css`, `tailwind.config.js` (fontFamily), `resources/views/default/header.blade.php`, `resources/views/cpanel/core/header-styles.blade.php`.

**Interfaces — Consumes:** Phase 1 tokens. **Produces:** `font-serif` (Newsreader), `font-sans` (Inter), `font-mono` (Geist Mono) all self-hosted; preload of the 2 critical weights; zero font CDN requests.

- [ ] **Step 1: Add the variable fonts.**
```bash
npm install --save-dev @fontsource-variable/newsreader @fontsource-variable/inter @fontsource-variable/geist-mono
```

- [ ] **Step 2: Create `resources/css/fonts.css`** importing the variable faces (full axis), imported by both bundles after tokens:
```css
@import '@fontsource-variable/newsreader';        /* opsz + wght + italic */
@import '@fontsource-variable/inter';
@import '@fontsource-variable/geist-mono';
```
Add `@import './fonts.css';` near the top of `app.css` and `admin.css`. (If the perf-budget subset later requires it, switch to explicit `@font-face` with `unicode-range` subsets and local woff2 in `resources/fonts/` — note this as the budget-tightening follow-up.)

- [ ] **Step 3: Update `fontFamily`** in `tailwind.config.js`:
```js
fontFamily: {
  serif: ['"Newsreader Variable"', ...defaultTheme.fontFamily.serif],
  sans:  ['"Inter Variable"', ...defaultTheme.fontFamily.sans],
  mono:  ['"Geist Mono Variable"', ...defaultTheme.fontFamily.mono],
},
```
(Confirm the exact CSS family names exposed by the installed `@fontsource-variable` packages and use those verbatim.)

- [ ] **Step 4: Remove the Google Fonts CDN** from `resources/views/default/header.blade.php` (delete the `preconnect` + `css2?family=Inter+Tight...Newsreader` `<link>`, lines ~49-51). Add a `preload` for the 2 critical weights (Newsreader 500, Inter 400/500) pointing at the built woff2 (use the Vite asset URL / `Vite::asset` or a `<link rel=preload as=font crossorigin>` to the fontsource file). Verify no `fonts.googleapis.com`/`gstatic` remains.

- [ ] **Step 5: Remove the Font Awesome MaxCDN `<link>`** from `cpanel/core/header-styles.blade.php`. Replace the few FA icons (admin user-card social icons) with inline SVG (Phase 6 introduces an `<x-icon>` set; for now inline the handful used). Grep `fa-` usage to enumerate.

- [ ] **Step 6: Build + verify no CDN + suite green.**
Run: `npm run build 2>&1 | tail -5 && grep -rn "googleapis\|gstatic\|maxcdn\|bootstrapcdn" resources/views || echo "NO font CDN remains" && ./vendor/bin/pest 2>&1 | tail -5`
Expected: build OK; the grep prints "NO font CDN remains"; 322 passed.

- [ ] **Step 7: Commit.**
```bash
git add package.json package-lock.json resources/css/fonts.css resources/css/app.css resources/css/admin.css tailwind.config.js resources/views/default/header.blade.php resources/views/cpanel/core/header-styles.blade.php
git commit -m "design: self-host Newsreader/Inter/Geist Mono; remove Google Fonts + FA CDNs (DESIGN_SYSTEM §3/§7)"
```

---

## Phase 3 — Blade component library (§5)

**Files:** new `resources/views/components/**`; trim superseded classes from `app.css`/`admin.css` as components replace them.

**Interfaces — Produces** these `<x-*>` components, each implementing the §5 spec (variants, sizes, states, a11y attributes). Build them in dependency order; each gets a focused render test (a tiny Blade that renders the component is hit by a feature test asserting key attributes/classes, OR a `Pest` view test via `$this->blade('<x-button>Go</x-button>')`).

Component contracts (name → key props → spec ref):
- `x-button` → `variant=primary|secondary|outline|ghost|destructive`, `size=sm|md|lg`, `as=button|a`, `:loading`, `icon` → §5 Buttons (focus-visible 2px ring offset 2px; active scale .98; disabled; `aria-busy` on loading; icon-only requires `aria-label`).
- `x-badge` → `variant=neutral|primary|success|warning|error` → §5 Badges (radius-full, caption 500).
- `x-card` → slots `header`/`default`/`footer`, `:interactive` → §5 Cards.
- `x-card.post` → `:post` (eyebrow category, Newsreader h4 title, muted excerpt, mono date/author, optional 16:9 media) → §5 Post card.
- `x-alert` (persistent banner) → `variant=info|success|warning|error`, `:dismissible`, slot → §5 Alert (role alert/status; form-top summary links to fields).
- `x-breadcrumb` + `x-breadcrumb.item` → §5 Breadcrumbs (`nav[aria-label=Breadcrumb]`, mono caption, `aria-current=page` on last, separators `aria-hidden`).
- `x-pagination` → `:paginator` → §5 Pagination (mono labels, `nav[aria-label=Pagination]`, `aria-current=page`, disabled ends `--text-subtle`). Replaces the current `pretty_url()` markup.
- `x-avatar` → `:user`, `size=sm|md|lg` → §5 Avatar (radius-full, initials fallback, alt=name).
- `x-dropdown` (+ `x-dropdown.item`) → trigger slot + items; `aria-haspopup=menu`, `aria-expanded`, arrow/Esc keyboard, focus return; Alpine-backed.
- `x-modal` → `:id`, title slot, body, footer; focus-trapped, Esc + scrim close, `role=dialog aria-modal aria-labelledby` → §5 Modals. (Admin already has a modal runtime in `admin.js` — wrap/standardize it.)
- `x-tabs` (+ `x-tab`) → `role=tablist/tab/tabpanel`, `aria-selected/-controls`, arrow-key nav → §5 Tabs (primary use: per-locale content editing).
- `x-field` → `label`, `name`, `:error`, `help`, control slot → §5 Inputs/forms (label→control 6px, control→helper 4px; error sets `aria-invalid` + `aria-describedby`).
- `x-eyebrow` → mono uppercase letter-spaced kicker → §3.
- `x-empty-state` → icon/headline/explanation/CTA → §5 Empty states.
- `x-toast-region` → the live-region container the existing `adminToast()` / a new front toast renders into → §5 Toasts.
- `x-icon` → a small inline-SVG icon set (replaces Font Awesome) → used sitewide.

- [ ] **Step 1 (per component): Write a render test** (e.g. `tests/Feature/Components/ButtonTest.php`):
```php
it('renders a primary button with focus ring and aria-busy when loading', function () {
    $html = $this->blade('<x-button variant="primary" :loading="true">Save</x-button>');
    expect($html)->toContain('aria-busy="true"')->toContain('Save');
});
```
- [ ] **Step 2: Run it red, implement the component, run green.** `./vendor/bin/pest tests/Feature/Components/ButtonTest.php`.
- [ ] **Step 3: Repeat for each component above** (one commit per small cluster, e.g. "button+badge+eyebrow", "card+post-card", "alert+toast-region", "breadcrumb+pagination", "field", "dropdown+modal+tabs", "avatar+empty-state+icon").
- [ ] **Step 4: Full suite + build green; commit each cluster.** Plain messages, e.g. `git commit -m "design: add x-button/x-badge/x-eyebrow components (DESIGN_SYSTEM §5)"`.

---

## Phase 4 — Public shell (header, footer, breadcrumb, landmarks)

**Files:** `resources/views/default/header.blade.php`, `footer.blade.php`, `partials/banner.blade.php`; `resources/js/front.js` (focus-trap drawer); `tests/Browser/*` (data-testid + computed-style updates).

- [ ] **Step 1:** Rebuild the header per §5: sticky `--bg`→on-scroll `--border` bottom + `--shadow-sm` + `backdrop-blur`, height 64px; wordmark (Newsreader/mono lockup); nav links (Inter 500, muted→`--text` + `--primary` underline active); right cluster search affordance + locale switcher (mono, via `x-dropdown`) + auth/dashboard link; mobile hamburger → full-height drawer, **focus-trapped**, `Esc` closes. Add a skip-to-content link as the first focusable element. Use `<header>`, `<nav aria-label>`, `<main id="main">`.
- [ ] **Step 2:** Rebuild the footer per §5 (columns: wordmark+tagline, nav groups, locale switcher; bottom mono caption copyright + stack attribution; include the plugin render-region hook `@hook('footer')`). Keep `</main>` close + `@stack`.
- [ ] **Step 3:** Convert `partials/banner.blade.php` to use `x-breadcrumb` (mirrors the `BreadcrumbList` JSON-LD).
- [ ] **Step 4:** Focus-trap + `Esc` for the mobile drawer in `front.js` (reduced-motion safe).
- [ ] **Step 5:** Update `tests/Browser/AuthAdminTest.php`/`HomepageTest.php` `data-testid`s + computed-style assertions to the new shell. Run `./vendor/bin/pest 2>&1 | tail -5` (HTTP suite green; browser tests skip locally).
- [ ] **Step 6: Commit** `design: rebuild public header/footer/breadcrumb to DESIGN_SYSTEM §5 (a11y landmarks, focus-trapped drawer)`.

---

## Phase 5 — Public pages

**Files:** `default/pages/{home,page,contacts,search}.blade.php`, `default/posts/{post,modal}.blade.php`, `default/categories/category.blade.php`, `default/tags/tag.blade.php`, `default/users/{profile,yourprofile,change_password}.blade.php`, `resources/views/auth/{login,social,register,verify}.blade.php` + `auth/passwords/{email,reset}.blade.php`.

Apply the component library + §3 typography + §4 spacing. One commit per page-group; each keeps its feature tests green.

- [ ] **Step 1: Post detail** (`posts/post.blade.php`) — `<article>`, one h1 (Newsreader), mono eyebrow category, byline + `x-avatar`, `<time datetime>`, `.prose` Newsreader 68ch (§5 Prose: primary links underlined, blockquote 2px primary border, mono code on `--surface-2`), tag pills (`x-badge`), like bar (keep `postLike` island), comment thread + `x-field` comment form + `x-pagination`. Keep `posts/modal.blade.php` as `x-modal`. Verify `tests/Feature/Front/*PostInteraction*`, `SeoMeta` green.
- [ ] **Step 2: Archives** (`categories/category.blade.php`, `tags/tag.blade.php`) — `x-card.post` grid (`repeat(auto-fit,minmax(280px,1fr))`), `x-pagination`, `x-empty-state` when none. De-duplicate the shared date-badge pattern into the card. Verify `Tags/TagArchive`, front category tests green.
- [ ] **Step 3: Home** (`pages/home.blade.php`) — hero (display type, optional media with `width/height`+`fetchpriority=high`), posts-from-category section using `x-card.post`, team/about section using `x-card` + `x-avatar`. Keep `reveal` island (reduced-motion safe).
- [ ] **Step 4: Standard/contact/search pages** — `pages/page.blade.php` prose; `contacts.blade.php` form via `x-field` + `x-button` + `x-alert` (reuse the inline alert that's copy-pasted today); `search.blade.php` results list + `x-empty-state`. Verify `SearchContactLanguage` test green.
- [ ] **Step 5: User pages** — `users/profile.blade.php` (`x-avatar` lg, role `x-badge`, social via `x-icon`), `yourprofile.blade.php` (`x-field`s, avatar island), `change_password.blade.php` (`x-field` + form-top `x-alert` error summary). Verify `ProfileFlow`, `FrontendProfileUpdate` green.
- [ ] **Step 6: Auth views (incl. the 4 Bootstrap-4 ports)** — rebuild `register`, `passwords/email`, `passwords/reset`, `verify` from Bootstrap-4 markup to the component library; bring `login`/`social` to parity. Centered card (`x-card`), `x-field`s, `x-button` primary, `x-alert` for errors. Verify `tests/Feature/Auth/*` green (these assert the auth flows — markup change must not break them).
- [ ] **Step 7:** Update `tests/Browser/{HomepageTest,GeoSettingsTest}` assertions for the new pages. Full suite green after each step; **commit per group**.

---

## Phase 6 — Admin shell + admin components

**Files:** `cpanel/core/index.blade.php`, `cpanel/nav/left-nav.blade.php`, `cpanel/nav/top-nav.blade.php`, `cpanel/core/{flash,modals}.blade.php`; `resources/js/admin.js` (dark-mode toggle + focus-trap); `admin.css` dark variants.

- [ ] **Step 1: Sidebar** (§5) — fixed 260px, `--surface`, 1px `--border` right; mono uppercase `x-eyebrow` group labels; items icon (`x-icon`) + label (Inter 500); active = `--surface-2` + 2px `--primary` left bar + `--text`; permission-gated items hidden; off-canvas drawer `< lg`, focus-trapped.
- [ ] **Step 2: Topbar** (§5) — sticky 56px `--surface` + `--border` bottom; left section title (h4) + mobile menu button; right dark/light toggle + "View site ↗" + user avatar (`x-avatar`) → `x-dropdown` (role badge, profile, sign out).
- [ ] **Step 3: Dark-mode toggle** — `admin.js` toggles `.dark` on `<html>`/shell, persisted in `localStorage`, respects `prefers-color-scheme` initial. Verify both modes meet §2 contrast.
- [ ] **Step 4: Standardize flash → `x-alert`, custom-field modals → `x-modal`.** Keep the `admin.js` modal/collapse/toast runtime (or back the components by it).
- [ ] **Step 5: Build + suite green; update `tests/Browser/AuthAdminTest` (sidebar readability + dark toggle).** Commit `design: rebuild admin shell (sidebar/topbar/dark toggle) to DESIGN_SYSTEM §5`.

---

## Phase 7 — Admin section views (30 views)

**Files:** all `resources/views/cpanel/**` section views.

Apply admin components per §5. Group commits by section.

- [ ] **Step 1: Lists** (`*_list.blade.php` for posts/pages/categories/comments/users/roles/menus/plugins/revisions) — `<x-table>` (or the `.data-table` class standardized): `--surface` container, `--surface-2` mono-eyebrow `thead`, hover rows, trailing row-action `x-dropdown`, status `x-badge`; **bulk-selection bar** (§5: leading checkbox + select-all, bulk-action bar on ≥1 selected with count + actions + clear, destructive confirms via `x-modal`, `aria-live` selection count); status filter `x-tabs`; `x-pagination`; `x-empty-state`.
- [ ] **Step 2: Content forms** (`new_/edit_` posts/pages/categories, menus, users, roles) — `x-field`s; **per-locale editing via `x-tabs`** (the canonical translation UI); category parent picker; menu builder sortable list (§5 Sortable: drag handle + keyboard reorder + `aria-live`).
- [ ] **Step 3: Settings** (general/site/seo/geo) + **media** (`x-file-upload` dropzone per §5 with native `<input type=file>` fallback) + **dashboard** (`x-card` grid) + **revisions** (diff view).
- [ ] **Step 4: Full suite + build green; commit per section group.**

---

## Phase 8 — Performance + a11y pass (CI-measured)

**Files:** image markup across views; `header.blade.php` preload; CI (`.github/workflows/ci.yml`) Lighthouse job.

- [ ] **Step 1: Images** — responsive `srcset` + WebP/AVIF, explicit `width`/`height` (no CLS), `loading="lazy"` below fold, `fetchpriority="high"` on the LCP image; server-side thumbnails (verify the media pipeline emits sizes).
- [ ] **Step 2: Fonts** — confirm subset + preload of the 2 critical weights; total font ≤120KB (measure built woff2 sizes).
- [ ] **Step 3: A11y audit** — skip-link, landmarks, one-h1 + sequential headings on every template, focus order, focus-trap on modals/drawers, contrast (tokens already satisfy §2 — verify no off-token colors crept in), reduced-motion. The Pest browser suite's `assertNoAccessibilityIssues()` (WCAG 2.1 AA) covers this in CI.
- [ ] **Step 4: Lighthouse CI** — add a Lighthouse CI step to `ci.yml` (served app + headless Chrome) asserting Perf/SEO/A11y/Best-Practices ≥95 mobile, LCP/CLS/INP budgets. **FLAG:** cannot run in this sandbox (needs served app + Chrome + MySQL); the numbers come from the CI run — never assert an unrun score.
- [ ] **Step 5: Commit** `design: perf/a11y pass — responsive images, font preload, Lighthouse CI (measured in CI)`.

---

## Self-Review

**Spec coverage (vs DESIGN_SYSTEM.md):** §2 tokens → Phase 1 (exact hex, light+dark). §3 typography/fonts → Phase 2 (self-hosted Newsreader/Inter/Geist Mono) + base type in 1/4. §4 spacing/radius/containers → Phase 1 tokens, applied throughout. §5 every component → Phase 3 (library) + 4–7 (applied). §6 motion → Phase 1 tokens + reduced-motion in shells/islands. §7 perf budget → Phase 2 (fonts) + Phase 8 (images/Lighthouse, CI-measured). §8 SEO/a11y → landmarks/headings in 4–7, a11y audit in 8 + browser-suite `assertNoAccessibilityIssues`. All §5 components enumerated in Phase 3.

**Placeholder scan:** Exact token values are copied verbatim from §2. Component contracts name props + spec refs; the full Blade per component is written during execution against the §5 text (the spec IS the line-level guidance) — flagged where a value must be confirmed against the installed package (fontsource family names) rather than guessed.

**Known env limits (flagged, not failures):** Lighthouse/CWV and the route JS/CSS budgets and the browser computed-style suite need a served app + headless Chrome + MySQL — measured in CI, never asserted here. Font total-weight is measurable locally (built woff2 sizes).

**Risks:** (1) Mapping Tailwind `borderRadius`/`colors` to vars changes what existing utilities resolve to — Phase 1 keeps legacy ramps and audits `rounded-*`/color usage to avoid silent visual drift. (2) HTTP feature tests assert specific markup; each page slice must keep them green (don't remove asserted hooks — add `data-testid` rather than rename). (3) The 4 Bootstrap-4 auth views are the highest-risk ports (no Tailwind today). (4) Admin `admin.js` runtime (modal/collapse/toast/jQuery-shims) must keep working as views are componentized.
