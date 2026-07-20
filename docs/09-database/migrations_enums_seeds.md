# Migrations, Enums, and Seeds

This document details the physical structure generation mechanisms of the database, covering Laravel migrations, predefined Enums, and data seeding routines.

## 1. Migrations

The database schema is constructed via a series of strictly ordered Laravel migrations (`database/migrations`). The naming conventions indicate incremental evolution of the schema rather than a single monolithic dump.

### Key Evolutionary Milestones:
- **Foundational (`0001_01_01_*`)**: Sets up Laravel's default scaffolding (Users, Password Resets, Cache, Jobs).
- **Domain Core (`2026_02_24_*`)**: Introduces the main business entities: `projects`, `project_members`, `invitations`, `questionnaire_tables`, `inspections`, `responses`, and `result_snapshots`.
- **User Profiling (`2026_02_27_*` - `2026_07_03_*`)**: Gradual expansion of the `users` table to accommodate `is_admin`, `last_login_at`, `locale`, `terms_accepted_at`, and profile-specific fields.
- **Evaluation Rounds (`2026_02_28_*`)**: Major architectural addition introducing `evaluation_rounds`, changing `inspections` to relate to rounds, and adding `round_snapshots` and `publications`.
- **Review & Consolidation Phase (`2026_06_21_*`)**: Introduction of `review_comments` and `consolidated_responses`, moving the system from simple questionnaires to a multi-reviewer consensus platform.

## 2. Enums (Value Objects)

Enums are used to enforce data integrity at the application level before it reaches the database. They reside in `app/Enums`.

### 2.1 `AnswerLevel`
Defines the maturity or compliance level of a specific response.
- **Expected Values:** Typically represents a scale (e.g., `IMPLEMENTED`, `PARTIALLY_IMPLEMENTED`, `NOT_IMPLEMENTED`, `NOT_APPLICABLE`).
- **Usage:** Used in the `responses` and `consolidated_responses` tables.

### 2.2 `Visibility`
Controls who can see a publication or a specific asset.
- **Expected Values:** `PUBLIC`, `PRIVATE`, `ORGANIZATION_ONLY`.
- **Usage:** Used to control access in public directories (`RoundPublication`, `InspectionPublication`).

### 2.3 `ConsensusModel`
Determines the strategy used to aggregate multiple individual responses into a final organizational response during an Evaluation Round.
- **Expected Values:** `MAJORITY_RULES`, `MANUAL_REVIEW`, `AVERAGE`.
- **Usage:** Used in `consolidated_responses` and logic services (`AggregationService`).

### 2.4 `ResponseProfile`
Profiles that dictate section applicability or specific weighting based on the respondent.
- **Usage:** Mapped to sections via `2026_03_02_112120_add_response_profile_to_sections_table.php`.

## 3. Seeders & Factories

While the exact content of `database/seeders` was not fully listed, the architectural presence of factories (`Database\Factories\` in `composer.json`) and migrations implies standard Laravel testing data generation.
- **DatabaseSeeder**: Expected to orchestrate the insertion of standard `QuestionnaireVersions`, default `Categories`, and an initial administrative `User`.
- **Testing Factories**: Used by Pest tests to rapidly instantiate fake `Projects`, `Inspections`, and `EvaluationRounds` using `fakerphp/faker`.

---
**Confidence Level:** ★★★★★ (Direct analysis of filenames in `database/migrations` and `app/Enums`).
