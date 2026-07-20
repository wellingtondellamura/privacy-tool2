# Architectural Decision Records (ADRs)

This document contains inferred Architectural Decision Records (ADRs) based on the current state of the source code. These records explain *why* certain technological and structural choices were made.

## ADR 001: Use of Inertia.js for the Frontend
**Status:** Accepted
**Context:** The application needs a dynamic, fast, and responsive user interface typical of a Single Page Application (SPA), but maintaining a separate REST API and handling client-side state/routing adds significant overhead for a small/medium team.
**Decision:** We use Inertia.js to act as the glue between Laravel and Vue.js. 
**Consequences:**
- **Positive:** Allows developers to build an SPA using classic server-side routing and controllers. No need to build separate API endpoints for the main application UI. Data is passed directly as props.
- **Negative:** Harder to expose the exact same endpoints to third-party consumers (a separate API layer would be needed if public programmatic access is required).
**Evidence:** `composer.json` (`inertiajs/inertia-laravel`), `package.json` (`@inertiajs/vue3`), and `routes/web.php` returning `Inertia::render()`.

## ADR 002: Separation of Business Logic into Services and Actions
**Status:** Accepted
**Context:** The application domain involves complex workflows (e.g., closing an inspection, consolidating round results) and calculations (aggregation, divergences). Placing this logic inside Http Controllers violates the Single Responsibility Principle and makes the code hard to test and reuse.
**Decision:** We implement a Service and Action layer architecture.
- `app/Actions` handle single-purpose, state-changing workflows (e.g., `CloseRoundAction`).
- `app/Services` handle reusable calculations and data manipulation (e.g., `AggregationService`).
**Consequences:**
- **Positive:** High testability, reusable logic, slim controllers, clear domain boundaries.
- **Negative:** Increases the number of files and structural complexity.
**Evidence:** The existence of `app/Actions/CloseRoundAction.php` and `app/Services/AggregationService.php`.

## ADR 003: Use of Filament for the Administrative Panel
**Status:** Accepted
**Context:** The system requires an administrative back-office to manage master data (like base questionnaires, system configurations, and user management). Building CRUD interfaces from scratch in Vue/Inertia is time-consuming.
**Decision:** We use Filament (v4) for the admin panel.
**Consequences:**
- **Positive:** Extremely rapid development of complex CRUD interfaces, tables, and forms.
- **Negative:** Introduces a second frontend stack (Livewire + Alpine.js) alongside the primary stack (Inertia + Vue.js), increasing the learning curve for new developers.
**Evidence:** `composer.json` requiring `filament/filament: 4.0` and the `app/Filament` directory.

## ADR 004: Translation via Spatie Laravel Translatable
**Status:** Accepted
**Context:** The system needs to support multiple languages (pt_BR, en, es). Some database content (like questionnaire names or questions) must be localized natively.
**Decision:** We use `spatie/laravel-translatable` to store translations as JSON within single database columns rather than creating separate translation tables.
**Consequences:**
- **Positive:** Simplifies the database schema; querying is straightforward.
- **Negative:** Indexing and searching within JSON columns can be slower on very large datasets depending on the database engine.
**Evidence:** `composer.json` requiring `spatie/laravel-translatable: ^6.13`.

---
**Confidence Level:** ★★★★☆ (Strong hypothesis based on standard industry practices and explicit evidence in the repository).
