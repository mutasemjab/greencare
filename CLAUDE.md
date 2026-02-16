# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**GreenCare** is a healthcare and service management platform built with Laravel 9. It provides an admin dashboard for managing healthcare services and resources, and a REST API for mobile and external integrations.

## Getting Started

### Installation & Setup

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Set up environment variables
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan seed  # (if seeders exist)

# Build frontend assets
npm run build

# Run development server
php artisan serve
```

### Common Development Commands

```bash
# Frontend development (watch mode)
npm run dev

# Build frontend for production
npm run build

# Run all tests
php artisan test
php artisan test --parallel

# Run specific test file
php artisan test tests/Feature/SomeTest.php

# Run unit tests only
php artisan test tests/Unit

# Run feature tests only
php artisan test tests/Feature

# Code linting/formatting
./vendor/bin/pint

# Generate models, migrations, controllers from a single command
php artisan make:model ModelName -mrc

# Refresh database (warning: destructive)
php artisan migrate:refresh

# View registered routes
php artisan route:list

# Tinker shell (interactive exploration)
php artisan tinker
```

## Architecture & High-Level Structure

### Directory Organization

- **`app/Http/Controllers/Admin`** - Admin panel controllers for managing resources (doctors, patients, products, orders, etc.)
- **`app/Http/Controllers/Api/v1`** - API v1 endpoints organized by domain (User, Lab, Appointment, etc.)
- **`app/Models`** - Eloquent models representing database entities
- **`app/Services`** - Business logic services (OTP, Firebase messaging/rooms, notifications)
- **`app/Observers`** - Model observers that handle automatic events (e.g., sending notifications when orders are created)
- **`app/Traits`** - Reusable notification traits (`SendsAppointmentNotifications`, `SendsOrderNotifications`, `SendsRoomNotifications`)
- **`app/Helpers`** - Global helper functions for common operations (`uploadImage()`, `uploadFile()`)
- **`routes/api.php`** - API route definitions with versioning
- **`routes/admin.php`** - Admin panel routes (requires authentication)
- **`database/migrations`** - Database schema changes
- **`resources/views`** - Blade templates

### Authentication & Authorization

- **Admin users** use the `Admin` model with Spatie permission system
- **API users** use Sanctum tokens (user-facing) and Passport (optional)
- All admin routes require `auth:admin` guard
- Permissions are managed via Spatie's `spatie/laravel-permission` package
- Use `can()` checks to verify permissions before operations

### Key Integrations

1. **Firebase/Firestore** - Real-time messaging and room management
   - Services: `FirestoreMessageService`, `FirestoreRoomService`
   - Used for live updates and notifications

2. **OTP System** - Authentication via one-time passwords
   - Service: `OtpService`
   - Used for user registration and login

3. **Push Notifications** - Multi-channel notifications
   - Service: `AdminNotificationService`
   - Traits: `SendsAppointmentNotifications`, `SendsOrderNotifications`, `SendsRoomNotifications`
   - Triggered by model observers

4. **Payment Processing** - KNET payment gateway integration
   - Package: `asciisd/knet`

5. **Multi-Language Support**
   - Package: `mcamara/laravel-localization`
   - Language detection via middleware: `SetLocale`

6. **Data Export/Import**
   - Package: `maatwebsite/excel`
   - Export classes in `app/Exports`, Import classes in `app/Imports`

### Model Observers Pattern

Models use observers to trigger side effects automatically:

```php
// Example: When an Order is created, send notifications
OrderObserver::created() -> SendsOrderNotifications trait
```

Current observers:
- `OrderObserver` - Sends order notifications
- `MedicalTestObserver` - Sends medical test notifications
- `HomeXrayObserver` - Sends home xray notifications
- `AppointmentProviderObserver` - Sends appointment notifications
- `RequestNurseObserver` - Sends nurse request notifications
- `ElderlyCareObserver` - Sends elderly care notifications

When adding new models that need notifications, create an observer and register it in a service provider.

### API Versioning

API routes are organized under `v1` prefix in `routes/api.php`:
- Public routes (no auth required)
- Protected routes (require `auth:api` middleware)
- Domain-specific namespaces: User, Lab, Appointment, Provider, etc.

When adding new API endpoints, maintain this versioning structure for future compatibility.

### Database Migrations

- Migrations should use descriptive names with timestamps (auto-generated)
- Always create inverse migrations for `down()` method
- Check for deleted migrations in git status before creating new ones with similar names

Note: Current git status shows some old migration files marked for deletion. Use `git add` only the new dated migrations.

## Testing Strategy

- **Unit tests** - Test individual components/business logic (in `tests/Unit`)
- **Feature tests** - Test API endpoints and feature flows (in `tests/Feature`)
- Test database configuration is in `phpunit.xml`
- Run with `php artisan test` or PHPUnit directly

## Deployment Considerations

- Always run `composer install --no-dev` on production
- Always run migrations before deployment: `php artisan migrate --force`
- Ensure `.env` has correct values (database, API keys, Firebase credentials)
- Firebase and KNET credentials must be configured in `.env`
- Clear caches after deployment: `php artisan cache:clear config:cache route:cache`
