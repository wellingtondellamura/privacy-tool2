# Non-Functional Requirements (NFR)

This document lists the system constraints, quality attributes, and non-functional requirements inferred from the codebase architecture, tooling, and database design.

## NFR-01: Performance & Responsiveness (Inertia/Vue)
- **Description:** The system must behave like a Single Page Application (SPA), preventing full page reloads during navigation.
- **Implementation Constraint:** Must utilize Vue 3 and Inertia.js to handle client-side rendering while maintaining server-side routing.
- **Evidence:** `package.json` (`@inertiajs/vue3`).

## NFR-02: Internationalization & Localization (i18n)
- **Description:** The application must support multiple languages (Portuguese, English, Spanish) natively, extending to both the user interface and database models.
- **Implementation Constraint:** 
  - Backend validation and flash messages must use Laravel's localization (`lucascudo/laravel-pt-br-localization`).
  - Database text fields (like questionnaire questions) must use JSON columns via `spatie/laravel-translatable`.
  - Frontend Vue components must use `vue-i18n`.
- **Evidence:** `composer.json`, `package.json`, `routes/web.php` (`locale.update`).

## NFR-03: Security & Authorization
- **Description:** The system must rigorously control access to projects and inspections.
- **Implementation Constraint:**
  - Passwords must be hashed using Bcrypt (rounds = 12, per `.env.example`).
  - Authentication must be managed by Laravel Sanctum / Breeze.
  - Granular access control must be enforced via Laravel Policies (`app/Policies`).
- **Evidence:** `.env.example`, `routes/web.php` middleware (`auth`, `verified`).

## NFR-04: Portability & Database Agnosticism
- **Description:** The system must not be locked into a single relational database vendor.
- **Implementation Constraint:** Use Laravel's Eloquent ORM and generic Migrations to support at least MySQL, PostgreSQL, and SQLite.
- **Evidence:** `README.md` database support section, standard migration usage.

## NFR-05: Testability & Quality Assurance
- **Description:** The application must be highly testable at both the unit and end-to-end levels.
- **Implementation Constraint:**
  - Backend testing must be written using Pest PHP.
  - End-to-end frontend testing must utilize Playwright.
  - Code style must be enforced by Laravel Pint.
- **Evidence:** `composer.json` (`pestphp/pest`), `package.json` (`@playwright/test`).

## NFR-06: Data Immutability & Auditing
- **Description:** Core compliance data (like inspection results) must be preserved immutably once closed to serve as audit evidence.
- **Implementation Constraint:** 
  - Utilize "Snapshot" tables (`result_snapshots`, `round_snapshots`) to store JSON payloads representing the exact state and score calculations at the time of closure.
  - Use `SoftDeletes` on primary operational models.
- **Evidence:** `CloseRoundAction.php`, migrations for `result_snapshots`.

---
**Confidence Level:** ★★★★☆ (Inferred from technology choices, configuration files, and architectural patterns).
