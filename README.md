# Luma Gallery

**Luma Gallery** is a premium, editorial online art gallery built on WordPress — a portfolio project that demonstrates custom theme + plugin architecture, a hand-built SCSS/JS pipeline, and a full set of interactive features (favorites, AI-style curation, an immersive viewer, a demo commerce flow and an artist workspace) without relying on page builders or off-the-shelf themes.

> **Portfolio demo.** Everything is safe to explore: **no real payments are processed**, **no external/paid AI APIs are used** (the “AI” features are rule-based), and the demo commerce + artist tools run entirely in the browser via `localStorage`.

---

## Table of contents

- [Overview](#overview)
- [Features](#features)
- [Tech stack](#tech-stack)
- [Architecture](#architecture)
- [Repository structure](#repository-structure)
- [How it works](#how-it-works)
- [Getting started](#getting-started)
- [Customizer settings](#customizer-settings)
- [Build & development](#build--development)
- [Accessibility & responsiveness](#accessibility--responsiveness)
- [Author](#author)

---

## Overview

Luma Gallery presents artworks, artists, exhibitions and an editorial journal inside a single cohesive “digital gallery” experience. The visual language is **editorial / brutalist-clean**: hairline borders, oversized display typography, near-square corners, large background words and a restrained palette (paper `#ffffff`, ink `#1d1d1f`, accent `#fb472f`).

The project is split into a **presentation theme** and a **business-logic plugin**, with an additional **client-side demo layer** that simulates collecting and selling art end-to-end without a backend or third-party services.

---

## Features

### Browsing & discovery
- **Gallery, Artists, Exhibitions** listing pages with filtering and grid/wall layout switching.
- **Journal** — a real custom post type (`luma_artist_post`) with topics, archive at `/journal/`, and single posts.
- **Search** — a custom, editorial results page with post-type labels and a strong empty state.
- **Immersive viewing** — Gallery Walk and a “View in Room” modal.

### Collecting (client-side demo)
- **Favorites** — save artworks, artists and exhibitions (`localStorage`), with a dedicated `/favorites/` page.
- **Demo Cart → Checkout → Orders** — add to cart, run a full checkout with inline validation, and generate a demo order (`LUMA-YYYY-NNNN`). No real payment.
- **Account dashboard** — profile, demo order history, saved works, followed artists and recently-viewed items.

### Creating (client-side demo)
- **Artist Studio** (`/studio/`) — a demo artist workspace: add/edit demo artworks with a live preview, write studio posts, and use a **mock AI Assistant** (descriptions, story, tags, social copy, SEO) — all rule-based, stored locally.

### “AI” features (rule-based, no external APIs)
- **AI Curator** (`/ai-curator/`) — turns a sentence about your space/taste into a filtered selection.
- **AI Assistant** modal and the Studio assistant — template-driven text generation.

### Site completion
- **About** page, custom **404** (“missing artwork” gallery concept), and the editorial **Search** page.
- **Customizer** controls for hero image, demo notice and footer tagline.
- **Demo Content tool** — a one-click admin page that seeds (and clears) all demo pages and journal content.

---

## Tech stack

- **WordPress** (classic templates, custom theme + plugin)
- **PHP 7.4+**
- **SCSS** compiled with **Gulp** (Dart Sass → autoprefixed, minified `main.min.css`)
- **Vanilla JavaScript** — ES modules concatenated and minified (Terser) into a single `main.min.js`
- **`localStorage`** for all demo commerce / studio state
- **Local by Flywheel** for local development
- No external APIs, no real payment gateway, **WooCommerce optional** (all Woo calls are feature-gated; the site runs fully without it)

---

## Architecture

| Layer | Where | Responsibility |
| --- | --- | --- |
| **Custom Theme** | `themes/LumaGallery` | Templates, UI, SCSS, vanilla JS, Customizer |
| **Luma Core plugin** | `plugins/luma-core` | Journal CPT, `luma_post_topic` taxonomy, demo-data helpers, demo content seeder + admin tool |
| **Local Demo Layer** | browser `localStorage` | Favorites, cart, checkout, orders, account, studio, recently-viewed |

Business/content logic lives in the **plugin** so it stays independent of the active theme; the **theme** stays focused on presentation.

---

## Repository structure

This repository is organised like a real WordPress `wp-content` directory, so it can be dropped straight into an install:

```
.
├── .gitignore                     # tracks only the custom theme + plugin
├── plugins/
│   └── luma-core/
│       ├── luma-core.php          # bootstrap, activation hook
│       └── includes/
│           ├── post-types.php     # Journal CPT (luma_artist_post)
│           ├── taxonomies.php     # luma_post_topic taxonomy + topics
│           ├── demo-data.php      # Journal demo/fallback helpers
│           ├── demo-seeder.php    # one-click demo content seeder
│           └── admin.php          # "Luma Gallery → Demo Content" admin page
└── themes/
    └── LumaGallery/
        ├── functions.php          # setup, asset enqueue, Customizer
        ├── header.php / footer.php
        ├── front-page.php         # custom homepage (hero)
        ├── search.php / 404.php
        ├── single-*.php / archive-*.php
        ├── templates/             # page templates (Gallery, About, Cart, Checkout, Account, Studio…)
        ├── template-parts/        # cards, hero, homepage sections, modals
        ├── src/
        │   ├── scss/              # source styles (art-direction monolith + page partials)
        │   └── js/modules/        # source JS modules
        ├── assets/                # compiled main.min.css / main.min.js (committed)
        ├── gulpfile.js
        └── package.json
```

---

## How it works

### Demo data & content
Real WordPress records exist for the **Journal** (`luma_artist_post`). Artworks, artists and exhibitions are rendered from **built-in demo data** — the templates gracefully fall back to curated demo content when no real records exist, so every page looks complete out of the box. Demo artwork cards get a stable slug-based id so they can be favorited / added to cart independently.

### Client-side state (`localStorage`)
All demo collecting and studio data lives in the browser:

| Key | Purpose |
| --- | --- |
| `luma_favorites`, `luma_favorites_meta` | saved artworks / artists / exhibitions |
| `luma_demo_cart` | demo cart contents |
| `luma_demo_orders` | placed demo orders (`LUMA-YYYY-NNNN`) |
| `luma_recently_viewed` | recently viewed artworks |
| `luma_studio_artworks`, `luma_studio_posts`, `luma_studio_ai_count` | Artist Studio content & metrics |

### “AI” without AI
The AI Curator and the Studio/Assistant generators are **deterministic, rule-based** text/selection engines (phrase banks + filters). There are no network calls and no paid services.

---

## Getting started

> Requires a WordPress install (Local by Flywheel recommended) with this repo’s `plugins/luma-core` and `themes/LumaGallery` placed under `wp-content/`.

**Minimum setup (3 steps):**

1. **Activate the plugin** — *Plugins → Luma Core*. (WooCommerce is **not** required.)
2. **Seed demo content** — *Luma Gallery → Demo Content → “Seed Demo Content.”* This creates the 10 demo pages with the correct page templates, plus the Journal posts and topics.
3. **Save permalinks** — *Settings → Permalinks → “Post name” → Save Changes* (so `/journal/`, `/gallery/`, etc. resolve).

That’s enough to bring the whole site to a presentable state.

**Optional:**
- *Appearance → Customize → Luma Gallery Settings* — set a hero image, toggle the demo notice, edit the footer tagline.
- *Appearance → Menus* — assign a menu to **Primary Navigation** (the theme ships a working fallback menu otherwise).
- The homepage needs no configuration — the theme renders its custom `front-page.php` at `/`.

**Reset:** *Luma Gallery → Demo Content → “Clear Demo Content”* removes only the seeded pages/posts (marked with a `_luma_demo_content` meta) and never touches your own content.

---

## Customizer settings

*Appearance → Customize → Luma Gallery Settings:*

- **Hero Image** + **Alt text** — optional homepage hero (falls back to a bundled image).
- **Show demo notice** — toggles the small demo/portfolio disclaimer in the footer.
- **Footer Tagline** — the brand line in the footer.

---

## Build & development

Sources live in `themes/LumaGallery/src/`; WordPress only loads the compiled `assets/main.min.*`.

```bash
cd themes/LumaGallery
npm install
npm run build      # compile SCSS + JS into assets/
```

Notes:
- SCSS lives in `src/scss/` (the active visual system is the `pages/_art-direction.scss` monolith plus page partials); JS lives in `src/js/modules/`.
- JS modules are concatenated into one scope, so top-level names are uniquely prefixed per module.
- Dart Sass `@import` deprecation warnings are expected and can be ignored while the build succeeds.

---

## Accessibility & responsiveness

- Responsive and free of horizontal overflow from 360px up to large desktops.
- Semantic landmarks, labelled form controls, accessible tabs (`aria-selected` + `hidden` panels), `aria-live` feedback regions, visible focus states, and `aria-hidden` on decorative background words/frames.
- Honors `prefers-reduced-motion`.

---

## Author

Built by **Alexsey Zhulimov** as a portfolio project, with an AI-assisted development workflow.

> Status: feature-complete demo. Built as a showcase of custom WordPress development — not intended for production sites with real content or payments.
