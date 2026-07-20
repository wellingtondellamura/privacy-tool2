# Dependencies Map

This document outlines the core external dependencies and libraries utilized by the Mitra Privacy Tool, categorized by their domain. 

## Backend Dependencies (PHP / Laravel)
*Extracted from `composer.json`*

### Core Framework
- **`laravel/framework` (^12.0)**: The underlying PHP framework.
- **`php` (^8.2)**: The required PHP version.

### Authentication & Authorization
- **`laravel/breeze` (^2.3)**: Provides the starting scaffolding for authentication.
- **`laravel/sanctum` (^4.0)**: Provides a featherweight authentication system for SPAs and simple APIs.

### Frontend Integration
- **`inertiajs/inertia-laravel` (^2.0)**: Adapter to connect Laravel backend to the Vue.js SPA without building a traditional REST API.
- **`tightenco/ziggy` (^2.0)**: Used for sharing Laravel routes with the Javascript/Vue frontend.

### Admin Panel & UI
- **`filament/filament` (4.0)**: A collection of tools for rapidly building beautiful TALL stack interfaces, used here specifically for the admin panel.

### Data & Modeling
- **`spatie/laravel-translatable` (^6.13)**: Provides the ability to make Eloquent model attributes translatable (JSON columns).

### Development & Testing
- **`pestphp/pest` (^4.4) / `pestphp/pest-plugin-laravel` (^4.1)**: The modern testing framework used for unit and feature tests.
- **`laravel/pint` (^1.24)**: Code style fixer for PHP.
- **`laravel/sail` (^1.41)**: Docker development environment.
- **`fakerphp/faker` (^1.23)**: For generating fake data during testing and seeding.
- **`mockery/mockery` (^1.6)**: Mock object framework for testing.
- **`lucascudo/laravel-pt-br-localization` (^3.0)**: Portuguese language files for Laravel default messages.

---

## Frontend Dependencies (JavaScript / Vue)
*Extracted from `package.json`*

### Core Framework
- **`vue` (^3.4.0)**: The core Javascript framework used for the SPA.
- **`@inertiajs/vue3` (^2.0.0)**: The Inertia client-side adapter for Vue 3.

### Styling & UI
- **`tailwindcss` (^3.2.1)**: Utility-first CSS framework.
- **`@tailwindcss/vite` (^4.0.0)**: Vite plugin for Tailwind CSS.
- **`@tailwindcss/forms` (^0.5.3)**: Tailwind plugin for basic form styling.
- **`autoprefixer` (^10.4.12) / `postcss` (^8.4.31)**: Used alongside Tailwind for CSS transformations.

### Build Tools
- **`vite` (^7.0.7)**: Next-generation frontend tooling and bundler.
- **`laravel-vite-plugin` (^2.0.0)**: Integration of Vite with Laravel.
- **`@vitejs/plugin-vue` (^5.0.0)**: Vue plugin for Vite.

### Networking & Utilities
- **`axios` (^1.11.0)**: Promise-based HTTP client for the browser (mostly abstracted by Inertia, but available).
- **`vue-i18n` (^10.0.8)**: Internationalization plugin for Vue.js.

### Testing
- **`@playwright/test` (^1.61.1)**: Framework for end-to-end testing of the frontend.

---
**Confidence Level:** ★★★★★ (Directly extracted from dependency lock files and configurations).
