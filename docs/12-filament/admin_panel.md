# Filament Admin Panel

The Mitra Privacy Tool utilizes **Filament v4** to construct its administrative back-office. This panel is strictly for system administrators and is distinct from the primary user-facing Inertia.js application.

## 1. Scope and Purpose
The admin panel is primarily used to manage Master Data (like questionnaires) and oversee the entire platform (global users, global projects) without needing database-level access.

## 2. Resources (`app/Filament/Resources`)

Filament generates a full CRUD (Create, Read, Update, Delete) interface for each defined Resource. The following resources have been reverse-engineered from the codebase:

### 2.1 Master Data (Questionnaires)
- **`QuestionnaireVersionsResource`**: Manages the versioning of the assessment base.
- **`CategoriesResource`**: Manages the top-level grouping of topics.
- **`SectionsResource`**: Manages sub-groups within categories.
- **`QuestionsResource`**: The granular interface for defining question text (likely utilizing a Repeater/Translatable field for multi-language support), metadata, and expected answers.

### 2.2 System Oversight
- **`UsersResource`**: Allows admins to view, edit, or disable user accounts globally.
- **`ProjectsResource`**: Allows admins to oversee active workspaces across the entire platform.
- **`InspectionsResource`**: Provides visibility into individual assessments for auditing or support purposes.
- **`InvitationsResource`**: Allows tracking of pending or accepted invitations.
- **`ResultSnapshotsResource`**: Read-only (typically) view of locked JSON results for auditing.

## 3. UI/UX Paradigm
Because it uses Filament, the Admin Panel adheres to a specific paradigm:
- **TALL Stack:** Built on Tailwind CSS, Alpine.js, Laravel, and Livewire.
- **Forms & Tables:** Each resource contains a `form()` method defining the inputs (Text, Select, Repeaters) and a `table()` method defining the data grid (Columns, Filters, Actions, Bulk Actions).
- **Navigation:** Resources are automatically registered in the Filament sidebar.

---
**Confidence Level:** ★★★★★ (Directly extracted from `app/Filament/Resources`).
