# Functional Requirements (FR)

This document lists the core Functional Requirements of the Mitra Privacy Tool, reverse-engineered from the routing, models, and controllers.

## FR-01: Authentication & User Profile
- **Description:** The system must allow users to register, log in, and manage their profiles.
- **Sub-features:**
  - Update profile details (Name, Email, Password).
  - Accept terms of use (`terms_accepted_at`).
  - Change interface language (`locale`).
- **Evidence:** `routes/web.php` (`ProfileController`, `auth.php`).

## FR-02: Project Management
- **Description:** Users must be able to create, edit, and manage Projects (Workspaces) which group their privacy initiatives.
- **Sub-features:**
  - Configure Project settings (icon, color, `show_evaluations_to_all`).
- **Evidence:** `ProjectController`, `Project` model.

## FR-03: Team Collaboration & Invitations
- **Description:** Project owners/admins must be able to invite external or existing users to join a project via email tokens.
- **Sub-features:**
  - Send, resend, and revoke invitations.
  - Assign roles to members (e.g., owner, auditor, responder).
  - Manage existing members.
- **Evidence:** `InvitationController`, `ProjectController@updateMemberRole`.

## FR-04: Questionnaire Management (Admin)
- **Description:** System Administrators must be able to define structured questionnaires.
- **Sub-features:**
  - Versioning (`QuestionnaireVersion`).
  - Structuring via Categories and Sections.
  - Defining specific Questions with metadata (e.g., knowledge fields).
- **Evidence:** `Filament` admin panel, `QuestionnaireVersion` model migrations.

## FR-05: Inspections & Responses
- **Description:** Users must be able to start an Inspection and answer the questions based on the active Questionnaire.
- **Sub-features:**
  - Activate, save draft (Response), and close an inspection.
- **Evidence:** `InspectionController`, `ResponseController`.

## FR-06: Evaluation Rounds & Consensus
- **Description:** Project managers must be able to group Inspections into time-boxed Evaluation Rounds to measure organizational compliance.
- **Sub-features:**
  - Create and manage round states (open, review, closed).
  - Add review comments to individual responses (`ReviewCommentController`).
  - Resolve divergences and consolidate answers based on a consensus strategy (`ConsolidatedResponseController`).
- **Evidence:** `EvaluationRoundController`, `CloseRoundAction`.

## FR-07: Results & Reporting
- **Description:** The system must calculate and display compliance scores and divergences.
- **Sub-features:**
  - View individual inspection results.
  - View team/round consolidated results.
  - Compare results between different rounds or inspections.
- **Evidence:** `ResultController`, `AggregationService`.

## FR-08: Public Directory & Badges
- **Description:** Organizations must be able to publicly showcase their privacy compliance.
- **Sub-features:**
  - Publish an inspection or round to the Public Directory.
  - Generate an embeddable HTML/JS badge displaying the current compliance medal.
  - Customize badge styling.
- **Evidence:** `PublicDirectoryController`, `RoundBadgeController`.

## FR-09: Data Export
- **Description:** Users must be able to export their data for portability and compliance reasons.
- **Sub-features:**
  - Export full project data.
  - Export complete user profile data.
- **Evidence:** `DataExportController`.

---
**Confidence Level:** ★★★★★ (Directly mapped from controller routes and HTTP methods).
