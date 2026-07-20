# Domain Model

This document outlines the core entities, value objects, and aggregates of the Mitra Privacy Tool, forming the foundation of the system's business logic.

## 1. Core Entities and Aggregates

### 1.1 Project Aggregate
- **Project**: The central organizational unit. Represents a compliance initiative or workspace for a company/team.
- **ProjectMember**: Associates a `User` with a `Project`, determining their level of access (e.g., owner, admin, auditor).
- **Invitation**: A temporary entity used to onboard new members into a Project via a secure token.

### 1.2 Questionnaire Aggregate (Master Data)
- **QuestionnaireVersion**: Represents a specific snapshot of the rules and questions. Allows the system to evolve questions over time without breaking historical results.
- **Category & Section**: Organizational units that group questions together logically. Categories group Sections.
- **Question**: The atomic unit of assessment. Can hold specific metadata (e.g., knowledge fields, weight) and belongs to a Section.

### 1.3 Assessment Aggregate
- **EvaluationRound**: A time-boxed or logical phase within a Project designed to gather multiple inspections, review them, and consolidate them into a final organizational posture.
- **Inspection**: An instance of answering a questionnaire. It is tied to a specific `Project` and `EvaluationRound`, and usually assigned to a specific `User`.
- **Response**: A user's specific answer to a `Question` within an `Inspection`.
- **ReviewComment**: Feedback left by reviewers on individual responses during the `review` phase of an EvaluationRound.
- **ConsolidatedResponse**: The definitive answer for a question within an EvaluationRound, after reviewers have resolved any divergences among individual `Responses`.

### 1.4 Result & Publication Aggregate
- **ResultSnapshot** / **RoundSnapshot**: Immutable records of the calculated scores/metrics at the time an Inspection or Round is closed.
- **RoundPublication** / **InspectionPublication**: Entities that control the public visibility of specific assessment results in the Public Directory.
- **RoundBadge**: Represents an embeddable script/HTML snippet tied to an EvaluationRound, allowing external sites to showcase their compliance status.

## 2. Value Objects (Enums)

Located in `app/Enums`, these define strict, constrained states:
- **`AnswerLevel`**: Likely defines the options a user can select (e.g., Fully Implemented, Partially Implemented, Not Implemented).
- **`Visibility`**: Determines if a publication is public, private, or restricted.
- **`ConsensusModel`**: Dictates how individual responses are merged into a `ConsolidatedResponse` (e.g., majority rules, manual review).
- **`ResponseProfile`**: Profiles applied to sections to dynamically adjust the required answers.

## 3. Relationships & Cardinalities
- A `Project` **has many** `Members`, `Invitations`, and `EvaluationRounds`.
- An `EvaluationRound` **has many** `Inspections` and **has many** `ConsolidatedResponses`.
- An `Inspection` **belongs to** a `Project`, an `EvaluationRound`, and a `QuestionnaireVersion`.
- An `Inspection` **has many** `Responses`.
- A `QuestionnaireVersion` **has many** `Categories`, which **have many** `Sections`, which **have many** `Questions`.

---
**Confidence Level:** ★★★★★ (Directly mapped from `app/Models` and `database/migrations`).
