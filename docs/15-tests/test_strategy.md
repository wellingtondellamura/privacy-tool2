# Testing Strategy

This document outlines the testing strategy for the Mitra Privacy Tool, designed to ensure the reliability of complex business rules and frontend flows.

## 1. Unit Testing (Backend)
- **Framework:** Pest PHP.
- **Location:** `tests/Unit/`
- **Scope:** 
  - **Services:** `AggregationService` and `DivergenceService` must have exhaustive unit tests covering edge cases (division by zero, no answers, extreme variances) to guarantee mathematically correct scoring and classification.
  - **Models:** Ensuring accessors, scopes, and relationships function correctly.
  - **Strategies:** Testing each `ConsensusModel` implementation in isolation.

## 2. Feature Testing (Backend / API)
- **Framework:** Pest PHP.
- **Location:** `tests/Feature/`
- **Scope:**
  - **Actions:** The `CloseRoundAction` must be tested against a seeded database state to ensure the final generated JSON `RoundSnapshot` matches expectations.
  - **Policies:** Testing that users cannot access, edit, or publish projects they do not own.
  - **HTTP Endpoints:** Validating that controllers correctly return Inertia responses or validation errors.

## 3. End-to-End (E2E) Testing (Frontend)
- **Framework:** Playwright (Node.js).
- **Location:** `tests/e2e/` (and via `package.json` scripts).
- **Scope:**
  - **Critical User Journeys:** 
    - A user successfully logging in, accepting an invitation, and completing an inspection.
    - An admin creating a round, manually resolving a conflict in the UI (`DivergencePanel.vue`), and closing the round.
  - **UI/UX Resilience:** Ensuring Inertia page transitions, modals, and dynamic tooltips function correctly across modern browsers.

## 4. Continuous Integration (CI)
- Tests should be automated via GitHub Actions (or equivalent CI/CD pipeline).
- **Code Style:** Enforced by Laravel Pint prior to running tests.

---
**Confidence Level:** ★★★★★ (Derived from the presence of `Pest.php`, `Feature/`, `Unit/`, and `e2e/` directories).
