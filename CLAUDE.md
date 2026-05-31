# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**GreenCare** is a healthcare and service management platform built with Laravel 9 (PHP ^8.0.2). It provides an admin dashboard for managing healthcare services and resources, and a REST API for mobile/external integrations.

## Commands

```bash
# Development
php artisan serve
npm run dev

# Build
npm run build

# Testing
php artisan test
php artisan test tests/Feature/SomeTest.php
php artisan test tests/Unit

# Linting
./vendor/bin/pint

# Database
php artisan migrate
php artisan migrate:refresh   # destructive
php artisan tinker

# Scaffolding
php artisan make:model ModelName -mrc
php artisan route:list
```

## Architecture

### Directory Layout

- `app/Http/Controllers/Admin/` — 45+ flat (no sub-namespaces) admin CRUD controllers
- `app/Http/Controllers/Api/v1/User/` — User-facing API controllers
- `app/Http/Controllers/Api/v1/Lab/` — Lab-facing API controllers
- `app/Models/` — 57+ Eloquent models; many use `spatie/laravel-translatable` for multi-language fields
- `app/Services/` — Business logic: `OtpService`, `AdminNotificationService`, `LabNotificationService`, `FirestoreMessageService`, `FirestoreRoomService`
- `app/Observers/` — Model side-effects (all registered in `EventServiceProvider::boot()`)
- `app/Traits/` — `SendsAppointmentNotifications`, `SendsOrderNotifications`, `SendsRoomNotifications`, `Responses` (API response formatting)
- `app/Helpers/General.php` — `uploadImage()`, `uploadFile()` (auto-loaded via composer)
- `app/Helpers/AppSetting.php` — Application settings helpers (auto-loaded via composer)

### Authentication Guards

Six guards are configured in `config/auth.php`:

| Guard | Driver | Usage |
|-------|--------|-------|
| `admin` | session | Admin panel (`auth:admin`) |
| `user` | session | Web user sessions |
| `user-api` | passport | API endpoints for patients/users (`auth:user-api`) |
| `lab` | session | Lab staff web sessions |
| `lab-api` | passport | API endpoints for labs (`auth:lab-api`) |
| `web` | session | Default Laravel web guard |

Admin routes use session auth; all API routes use Laravel Passport tokens. Sanctum is not used — Passport handles all API tokens.

### API Routes (`routes/api.php`)

Two main versioned groups under `api/v1/`:

- **`v1/user/*`** — Patient/user endpoints. Public: auth (OTP/login), banners, products, categories. Protected (`auth:user-api`): addresses, cart, orders, appointments, medications, notifications.
- **`v1/lab/*`** — Lab endpoints. Public: lab login. Protected (`auth:lab-api`): appointments, result uploads.

### Admin Routes (`routes/admin.php`)

All routes require `auth:admin`. Wrapped in `LaravelLocalization::setLocale()` middleware for multi-language URL prefixes. Uses `Route::resource()` throughout with additional custom routes for state toggles and bulk operations.

### Model Observers Pattern

Observers decouple notification side-effects from controllers. All six are registered explicitly in `EventServiceProvider::boot()`:

- `OrderObserver` → `Order`
- `MedicalTestObserver` → `MedicalTest`
- `HomeXrayObserver` → `HomeXray`
- `AppointmentProviderObserver` → `AppointmentProvider`
- `RequestNurseObserver` → `RequestNurse`
- `ElderlyCareObserver` → `ElderlyCare`

When adding a model that needs notifications: create an Observer, add the notification logic via the relevant `Sends*Notifications` trait, and register in `EventServiceProvider`.

### Key Integrations

- **Firebase/Firestore** — `kreait/firebase-php` + `google/cloud-firestore`. `FirestoreRoomService` is registered as a singleton in `AppServiceProvider`. Configure credentials via `FIREBASE_CREDENTIALS` in `.env`.
- **OTP Auth** — `OtpService` handles OTP generation/validation for user login/registration.
- **Push Notifications** — `AdminNotificationService` / `LabNotificationService`; triggered by observers via notification traits.
- **KNET Payments** — `asciisd/knet` package. Requires KNET API keys in `.env`.
- **Multi-Language** — `mcamara/laravel-localization` + `spatie/laravel-translatable`. `SetLocale` middleware reads locale from URL prefix; model string fields use translatable casts.
- **Permissions** — `spatie/laravel-permission` (v5) on the `Admin` model. Use `can()` checks before any admin operation.
- **Excel Export/Import** — `maatwebsite/excel`; export classes in `app/Exports/`, import classes in `app/Imports/`.

### Testing

- Unit tests: `tests/Unit/` — isolated component logic
- Feature tests: `tests/Feature/` — API and feature flows
- Test environment uses in-memory drivers (cache: array, session: array, queue: sync, mail: array)
- Database for tests is not SQLite in-memory by default — check `phpunit.xml` and `.env.testing` before running
