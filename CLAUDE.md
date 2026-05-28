# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Tech Stack

- **Laravel 11** — PHP framework
- **Tailwind CSS v4** — via `@tailwindcss/vite`, NOT via CDN or postcss.config
- **Vite** — build tool; `npm run dev` for development, `npm run build` for production
- **No Alpine.js** — vanilla JS only. Shared JS is in `resources/js/`, imported via `resources/js/app.js`
- **No database testing** — no PHPUnit setup

## Common Commands

```bash
php artisan serve               # Run dev server on localhost:8000
php artisan route:list          # List all routes
php artisan migrate             # Run migrations
php artisan migrate:rollback     # Rollback last migration batch
php artisan db:seed             # Seed database

npm run dev                     # Vite dev server (hot reload)
npm run build                   # Production build → public/build/
```

When adding new JS files to Vite: import them in `resources/js/app.js` before running `npm run build`, or use `@push('styles')`/`@vite()` only after the file is registered in the manifest.

## Architecture

### Roles

Two roles: `admin` and `manager` (PJ greenhouse). `kasir` is defined in the User model but not wired to routes yet.

- `admin`: full access to all routes under `/admin`, all greenhouse sales, all kasir features
- `manager`: gh.access-gated routes only — can manage trees/fruits/material-requests within their assigned greenhouses and view sales (read-only)
- Role helpers: `isAdmin()`, `isManager()`, `isKasir()` on the User model
- Role middleware: `middleware(['role:admin'])` on admin routes, `middleware(['gh.access'])` on greenhouse-scoped manager routes

### Middleware

- `gh.access` (`GreenhouseAccessMiddleware`): admin bypasses all checks; manager must be assigned to the greenhouse. Throws 403 view if unauthorized.
- `role:admin` (`RoleMiddleware`): checks `role` string on the authenticated user

### Route Patterns

All greenhouse-scoped resources use nested routes: `/greenhouse/{greenhouse}/trees/{tree}/fruits/{fruit}`.

Controllers receive route-model binding: `function show(Greenhouse $greenhouse, Tree $tree, Fruit $fruit)`. Never manually `findOrFail` for these.

The `gh.access` middleware resolves `$request->route('greenhouse')` and validates access.

### Sales — Greenhouse-Scoped

Sales are always scoped to a greenhouse. The `Sale` model has `greenhouse_id`. Routes live under `Route::prefix('sales')` and are name-prefixed `sales.`.

Key routes:
- `GET /sales` → greenhouse selector list with daily/monthly revenue summary
- `GET /sales/greenhouse/{gh}` → GH kasir dashboard (revenue stats, 10 recent transactions)
- `GET /sales/greenhouse/{gh}/create` → POS form
- `GET /sales/greenhouse/{gh}/report` → sales summary by melon variety (transactions, total weight, total revenue)
- `GET /sales/{sale}/print` → standalone HTML receipt (no layout extension)

The `SaleController` also enforces `abortIfCannotAccessSale()` — admin can access all sales, managers only sales from their greenhouses.

### Views

- All views use `layouts/app.blade.php` (navbar, bottom nav, Tailwind)
- Print views (`sales/print.blade.php`) are standalone HTML — no `@extends`, no navbar, no footer
- Views are in `resources/views/greenhouse/` (tree/fruit management) and `resources/views/sales/` (kasir)
- Admin views: `resources/views/admin/`
- UI uses stone/emerald color palette with rounded-2xl cards and Tailwind custom classes

### Database

Migrations use timestamp prefixes (`2026_05_28_*`). Key tables:
- `greenhouses` — name, code, description, is_active
- `trees` — greenhouse_id, melon_variety_id, tree_number (unique per GH), status (alive/dead)
- `fruits` — tree_id, condition (good/rotten), grade (A/B/C/D), weight (kg, decimal 6,2)
- `melon_varieties` — name only (admin-managed, globally shared)
- `material_requests` — greenhouse_id, user_id, material_name, quantity, unit, notes, status (pending/fulfilled)
- `sales` — greenhouse_id, user_id, buyer_name, total
- `sale_items` — sale_id, melon_variety_id, weight_kg, price_per_kg, subtotal