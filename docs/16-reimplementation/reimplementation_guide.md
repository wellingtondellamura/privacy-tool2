# Spec-Driven Re-implementation Guide

This document is the master guide for rebuilding the Mitra Privacy Tool entirely from scratch, relying exclusively on the extracted specification (`docs/`) rather than the legacy source code.

## 1. Project Initialization

1. **Stack Setup:** Initialize a new repository using Laravel 11/12, Vue 3, Inertia.js, and Tailwind CSS.
2. **Read the Architecture:** Review `docs/08-architecture/general_architecture.md` and `docs/03-requirements/non_functional_requirements.md` to understand the monolith constraints and dependency choices (Pest, Playwright, Filament).
3. **Design System:** Configure `tailwind.config.js` with the tokens found in `docs/11-design-system/tokens_and_components.md`.

## 2. Database & Domain Modeling

1. **Entities:** Review `docs/01-domain/domain_model.md` to understand the core vocabulary.
2. **Schema Definition:** Implement migrations in the exact order specified in `docs/09-database/migrations_enums_seeds.md`.
3. **Relationships:** Follow the Entity-Relationship Diagram (ERD) in `docs/09-database/mer_der.md` to establish Eloquent relations (`HasMany`, `BelongsToMany`, JSON casts).
4. **Enums:** Create the core PHP Enums (`AnswerLevel`, `ConsensusModel`, `Visibility`).

## 3. Core Business Logic (Test-Driven)

Before building any UI or HTTP Controllers, implement the core mathematical and state-transition logic using Test-Driven Development (TDD) via Pest PHP (`docs/15-tests/test_strategy.md`).

1. **Scoring Formulas:** Implement `AggregationService.php` based on `docs/02-business-rules/br_001_scoring_formula.md`. Write tests asserting correct rounding and medal distribution.
2. **Divergence Logic:** Implement `DivergenceService.php` based on `docs/02-business-rules/br_002_divergence_resolution.md`. Write tests asserting variance calculation and classification.
3. **State Machine:** Implement `CloseRoundAction.php` based on `docs/02-business-rules/br_003_round_closure_flow.md`. Write feature tests mimicking various consensus models and data shapes.

## 4. API & Backend Wiring

1. **Security Setup:** Implement Policies (`ProjectPolicy`) matching the rules in `docs/07-security/authorization.md` and `docs/07-security/threat_model.md`.
2. **Routing:** Build the Controllers and HTTP routes outlined in `docs/06-api/endpoints.md`. Ensure that they return valid Inertia responses.
3. **Filament Admin:** Scaffold the Master Data CRUD interfaces using Filament v4, following `docs/12-filament/admin_panel.md`.

## 5. Frontend & UI Construction

1. **Components:** Build the reusable Vue 3 atoms and molecules detailed in `docs/13-frontend/vue_structure.md`. Ensure forms utilize Inertia's `useForm` (see `docs/13-frontend/inertia_flow.md`).
2. **User Journeys:** Implement the Pages and flows (Responder flow, Reviewer flow) according to the User Stories in `docs/04-user-stories/` and `docs/05-use-cases/use_cases_uml.md`.
3. **UX & Accessibility:** Apply the heuristics and navigation paths defined in `docs/10-ux/flows_and_accessibility.md`.

## 6. Final Validation

1. Run all Pest Unit and Feature tests.
2. Write and execute Playwright end-to-end tests covering the critical user journeys (Project creation, Inspection answering, Round consolidation).
3. Confirm that all Functional Requirements (`docs/03-requirements/functional_requirements.md`) have been met.
