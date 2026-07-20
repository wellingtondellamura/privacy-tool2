# UX Flows & Accessibility

This document synthesizes the User Experience patterns and accessibility heuristics observed in the Mitra Privacy Tool.

## 1. Primary User Journeys (Flows)

### 1.1 The "Responder" Flow (Inspection)
1. **Entry:** User accesses their Dashboard and sees an active "Open Inspection".
2. **Action:** Clicks to resume or start.
3. **Execution:** The interface likely uses a wizard or categorized list (via `Category` and `Section` models). Users fill out `QuestionCard.vue` components.
4. **Friction Reduction:** Drafts are saved automatically (via `ResponseController@store` logic). Users can jump between sections.
5. **Exit:** Clicks "Close Inspection" (`ConfirmModal.vue` is likely triggered to prevent accidental closure).

### 1.2 The "Reviewer" Flow (Evaluation Round)
1. **Entry:** Admin accesses a Round in the `review` state.
2. **Analysis:** The dashboard (`DivergencePanel.vue`) highlights questions with `high` variance (calculating divergence).
3. **Collaboration:** Admin clicks into a diverging question to read `ConflictThread.vue` and `ReviewComment`s from team members.
4. **Resolution:** Admin uses `OwnerDecidePanel.vue` to override and save a `ConsolidatedResponse`.
5. **Exit:** Admin closes the round, finalizing the scores.

## 2. Information Architecture (IA)

The system relies on a strict hierarchical data structure that must be reflected in the UI navigation (Sidebar / Breadcrumbs).

- **Global Level:** Public Directory, User Profile.
- **Workspace Level:** Project Settings, Team Management.
- **Assessment Level:** Evaluation Rounds -> Inspections -> Categories -> Sections -> Questions.

*Component Evidence:* `Breadcrumbs.vue` exists to help users traverse this deep hierarchy without getting lost.

## 3. UI Heuristics & Feedback

- **Visibility of System Status:**
  Badges (`Badge.vue`, `ConsensusBadge.vue`) are used extensively to show whether an inspection is open, a question is divergent, or a round is closed.
- **Error Prevention:**
  Destructive actions (like closing an inspection or revoking an invitation) are guarded by `ConfirmModal.vue`.
  Validation errors from Laravel are automatically piped into Inertia and displayed via `InputError.vue` right beneath the offending `Input.vue`.
- **Help and Documentation:**
  `KnowledgeTooltip.vue` provides on-demand contextual help for complex privacy questions without cluttering the main interface.

## 4. Accessibility (A11y) inferred practices
- **Forms:** The presence of `InputLabel.vue` indicates a commitment to proper `<label for="...">` associations, which is crucial for screen readers.
- **Focus States:** The custom Tailwind config specifies primary focus colors (`brand-500`), indicating that keyboard navigation (tabbing) is visually supported.
- **Language:** Dynamic locale switching (`LocaleSwitcher.vue`) allows the `<html lang="...">` attribute to update, ensuring screen readers pronounce text correctly based on the user's preference.

---
**Confidence Level:** ★★★★☆ (Inferred strongly from the presence and naming of Vue components and Tailwind structures).
