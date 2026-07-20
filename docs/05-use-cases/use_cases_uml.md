# Use Cases (Textual UML)

This document contains the Textual UML representation of the core use cases for the Mitra Privacy Tool.

## Actors
- **Registered User**: A basic user authenticated in the system.
- **Project Admin**: A user with administrative rights over a specific project.
- **System Admin**: A user with global access to the Filament back-office.
- **Public Visitor**: An unauthenticated user viewing public directories or badges.

---

## UC-01: Manage Projects
- **Actor:** Registered User
- **Pre-condition:** User is logged in.
- **Main Flow:**
  1. User navigates to the dashboard.
  2. User selects "Create Project".
  3. System prompts for project details (Name, settings).
  4. User submits.
  5. System creates the project and assigns the user as "owner".
- **Alternative Flow (Edit):**
  1. Project Admin selects an existing project.
  2. Admin modifies settings (e.g., `show_evaluations_to_all`).
  3. System updates the project.

## UC-02: Manage Questionnaires
- **Actor:** System Admin
- **Pre-condition:** Admin is logged into the Filament panel.
- **Main Flow:**
  1. Admin creates a new `QuestionnaireVersion`.
  2. Admin defines `Categories` and nested `Sections`.
  3. Admin adds `Questions` to the sections, specifying text in multiple languages (JSON).
  4. System saves the master data, making it available for new projects.

## UC-03: Execute Inspection
- **Actor:** Registered User (Project Member)
- **Pre-condition:** An Evaluation Round is open in the Project.
- **Main Flow:**
  1. Member starts a new Inspection assigned to them.
  2. System loads the active Questionnaire.
  3. Member submits a `Response` for each `Question` (e.g., "Fully Implemented").
  4. Member optionally adds notes.
  5. Member clicks "Close Inspection".
  6. System calculates the `ResultSnapshot` for this individual inspection and locks the answers.

## UC-04: Consolidate Evaluation Round
- **Actor:** Project Admin
- **Pre-condition:** Evaluation Round is in `review` state.
- **Main Flow:**
  1. Admin views the divergence matrix.
  2. For questions with high variance, Admin reads `ReviewComments`.
  3. Admin overrides or confirms the final answer (`ConsolidatedResponse`).
  4. Admin clicks "Close Round".
  5. System executes `CloseRoundAction` (BR-003) and generates the final `RoundSnapshot`.

## UC-05: Publish Results (Badge)
- **Actor:** Project Admin
- **Pre-condition:** Evaluation Round is `closed`.
- **Main Flow:**
  1. Admin selects a closed Round.
  2. Admin opts to generate a Public Badge.
  3. System creates a `RoundBadge` token.
  4. Admin copies the JS snippet and places it on their external website.
- **Post-condition:** Public Visitors can see the compliance medal on the external site.

---
**Confidence Level:** ★★★★★ (Derived directly from business rules and routing).
