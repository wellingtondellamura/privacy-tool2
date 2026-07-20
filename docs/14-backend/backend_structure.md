# Backend Structure

This document details the organization of the Laravel backend, specifically focusing on how logic is distributed across standard MVC components and custom design patterns.

## 1. Controllers (`app/Http/Controllers`)
Controllers in this application primarily handle routing, authorization checks, and returning Inertia responses. They are designed to be "skinny", delegating complex logic to Services and Actions.

- **`ProjectController`**: CRUD for projects and member management.
- **`EvaluationRoundController`**: Manages the lifecycle of rounds (create, review, close). Uses `CloseRoundAction` for the heavy lifting.
- **`InspectionController`**: Handles user answering sessions.
- **`ResultController`**: Handles the display and comparison of generated snapshots.
- **`ConsolidatedResponseController` / `ReviewCommentController`**: Manage the interactive review phase of rounds.
- **`RoundBadgeController` / `PublicDirectoryController`**: Handle external-facing or embeddable content.

## 2. Actions (`app/Actions`)
Actions encapsulate a single business use case that modifies application state. They follow the Command pattern.
- **`CloseRoundAction`**: Validates conditions, applies the Consensus strategy, computes final scores, creates a `RoundSnapshot`, and closes the round.
- **`CloseInspectionAction`**: Similar to the round action, but computes scores for an individual user and creates a `ResultSnapshot`.

## 3. Services (`app/Services`)
Services encapsulate domain logic and algorithms that do not inherently modify the system's state or can be reused across different actions.
- **`AggregationService`**: Contains math formulas for section scores, category scores, and medals.
- **`DivergenceService`**: Contains statistical variance calculations to classify divergences between team members.
- **`AnswerScoreResolver`**: Extracts canonical numerical scores from `AnswerLevel` enums.
- **`ComparisonService`**: Compares two snapshots to compute deltas and improvement metrics.

## 4. Strategies (`app/Strategies`)
*Inferred from usage within Actions.*
- The system implements a **Strategy Pattern** for the `ConsensusModel`. Depending on project settings (e.g., Majority Rules, Owner Decides, Highest Score), a specific strategy class is injected into the closure flow to resolve answer conflicts automatically.

## 5. Background Jobs & Events
*Inferred from `config/queue.php` and typical Laravel patterns, though specific classes were not deeply extracted.*
- Email notifications (like invitations) are queued to prevent blocking the UI.
- Events might be fired during state changes (e.g., `InspectionClosed`, `RoundClosed`) to decouple notification logic.

---
**Confidence Level:** ★★★★★ (Direct analysis of the `app/` folder structure).
