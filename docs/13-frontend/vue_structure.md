# Vue.js Component Structure

This document outlines the organization of the frontend source code within `resources/js`, mapping the reusable components, pages, and architectural boundaries of the Vue 3 application.

## 1. Directory Structure

The Vue application adheres to the standard Inertia.js structure:
- **`Pages/`**: Components that act as routing targets (Views).
- **`Components/`**: Reusable UI elements (Atoms, Molecules, Organisms).
- **`Layouts/`**: Wrappers that provide the global shell (navbar, sidebar).
- **`composables/`**: Extracted Vue 3 Composition API logic (e.g., `useFormatDate.js`).
- **`locales/`**: JSON/JS files for frontend translations (`vue-i18n`).

## 2. Pages by Module (`resources/js/Pages`)
Pages are structured into domain-specific folders to manage complexity.

- **`Auth/`**: Login, Register, Forgot Password flows.
- **`Project/` & `Workspace/`**: Project creation, settings, and member management dashboards.
- **`EvaluationRound/`**: Interfaces for creating rounds, listing active rounds, and the critical **Review Interface** where admins resolve divergences.
- **`Inspection/`**: The main assessment interface where users answer the active questionnaire.
- **`Results/` & `Comparison/`**: Dashboards displaying calculated Snapshots and visual comparisons between different rounds or users.
- **`PublicDirectory/`**: Unauthenticated pages rendering public publications and tools.
- **Root Pages**: `Welcome.vue`, `Dashboard.vue`, `Manual.vue`.

## 3. Core UI Components (`resources/js/Components`)
The components directory mixes standard generic UI (buttons, inputs) with highly specific domain components (organisms).

### 3.1 Generic UI (Design System Atoms)
- `Button.vue`, `PrimaryButton.vue`, `DangerButton.vue`
- `Modal.vue`, `ConfirmModal.vue`, `AlertModal.vue`
- `Input.vue`, `Checkbox.vue`, `Dropdown.vue`
- `Card.vue`, `Badge.vue`

### 3.2 Domain-Specific Organisms
- **`QuestionCard.vue`**: Renders a single question, its options, and captures the user's `Response`.
- **`DivergencePanel.vue` & `ConflictThread.vue`**: Used during the review phase of an Evaluation Round to display varying answers and review comments.
- **`MajorityResult.vue` & `OwnerDecidePanel.vue`**: UI components specific to the `ConsensusModel` strategies, showing how a final answer was reached.
- **`KnowledgeTooltip.vue`**: Displays additional metadata/help text for specific questions.
- **`ConsensusBadge.vue` & `DivergenceBadge.vue`**: Visual indicators of the mathematical divergence calculations (BR-002).
- **`ProjectIconPicker.vue` & `LocaleSwitcher.vue`**: Specialized controls for settings.

## 4. State Management
Given the Inertia.js architecture, there is no heavy global state management library (like Vuex or Pinia) implemented.
- **Server State**: Managed by Laravel and passed down via Inertia `props`.
- **Local State**: Managed within Vue components using `ref()` and `reactive()`.
- **Shared Data**: Global data (like the authenticated user or active locale) is passed via Inertia's `HandleInertiaRequests` middleware.

---
**Confidence Level:** ★★★★★ (Directly extracted from `resources/js` directory structure).
