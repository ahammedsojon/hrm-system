# HRM System — Agent Guide

## Stack

- **Backend:** Laravel 13, PHP 8.3+, MySQL, session-based auth (web guard, cookie/CSRF, no Sanctum)
- **Frontend:** Vue 3 SPA (`<script setup>` composition API), Pinia, Vue Router 5, Axios, Tailwind CSS 4 (CSS-first, no `tailwind.config.js`)
- **Build:** Vite 8 + `laravel-vite-plugin` + `@vitejs/plugin-vue` + `@tailwindcss/vite`
- **Testing:** PHPUnit 12 (SQLite in-memory via `phpunit.xml`)

## Architecture

- Single Blade view `resources/views/app.blade.php` mounts the Vue SPA at `<div id="app">`
- `routes/web.php`: catch-all `Route::view('/{any?}', 'app')->where('any', '^(?!api).*$')` — all non-API paths serve the SPA
- `routes/api.php`: auth (login/logout/user) + CRUD for employees, departments, designations
- All API routes use `middleware('auth')` with Laravel's session guard

## Directory Layout

| Directory | Purpose |
|---|---|
| `app/Models/` | Eloquent models (User, Employee, Department, Designation, Role) |
| `app/Services/` | Business logic (Auth/, Employee/, Department/, Designation/) |
| `app/Http/Controllers/Api/` | API controllers |
| `app/Http/Requests/` | Form request validation |
| `app/Http/Resources/` | API resource transformers |
| `resources/js/pages/` | Vue page components (SPA routes) |
| `resources/js/components/` | Reusable (`ui/`, `layout/`, `employees/`) |
| `resources/js/composables/` | Vue composables (`useAuth`, `useEmployees`, etc.) |
| `resources/js/stores/` | Pinia stores (currently only `auth.js`) |
| `resources/js/services/` | Axios API wrappers per domain |
| `resources/js/config/navigation.js` | Sidebar nav config |

## Key Commands

```bash
composer setup          # full setup: install, .env, key:generate, migrate, npm install, build
composer dev            # runs 4 parallel processes (server, queue, logs, Vite) via concurrently
composer test           # config:clear + php artisan test
php artisan test        # runs PHPUnit 12 (Unit + Feature)
./vendor/bin/pint       # Laravel Pint (PSR-12 linter)
```

## Testing

- Tests use SQLite `:memory:` — no external DB needed
- Test suites: `tests/Unit/`, `tests/Feature/`
- Run a single test: `php artisan test --filter=TestName`
- Run a single file: `php artisan test tests/Feature/SomeTest.php`

## Conventions & Quirks

- `.npmrc` sets `ignore-scripts=true` — `composer setup` passes `--ignore-scripts` to npm. Run `npm install --ignore-scripts` unless you need build scripts.
- `SESSION_DRIVER=database`, `QUEUE_CONNECTION=database`, `CACHE_STORE=database` — sessions, queue, and cache require DB tables (migrations exist).
- Tailwind CSS 4 uses CSS-first config. Edit `resources/css/app.css` for theme customizations. No `tailwind.config.js`.
- Models use PHP 8 attributes: `#[Fillable]`, `#[Hidden]`.
- Services use constructor property promotion with `readonly`.
- API client (`resources/js/services/api.js`) uses `withCredentials: true` (cookie/session auth), auto-redirects to `/login` on 401.
- Frontend auth flow: `router.beforeEach` checks `authStore.isInitialized` and calls `fetchUser()` once on first navigation.
- `emergency_contact` on Employee model is cast to `array` (JSON column).
- `basic_salary` is `decimal:2`.

## Workflow

1. `composer install && npm install --ignore-scripts`
2. Copy `.env.example` → `.env`, set DB credentials
3. `php artisan key:generate`
4. `php artisan migrate`
5. `composer dev` (starts all 4 processes)
6. `composer test` before committing
