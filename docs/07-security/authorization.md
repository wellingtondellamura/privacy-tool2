# Authorization & Access Control (ACL)

This document maps out the authorization layers of the Mitra Privacy Tool, covering global administration and project-scoped roles.

## 1. Global Roles

At the global application level, authorization is binary:
- **`is_admin` (Boolean):** If true, the user is a System Administrator.
  - **Capabilities:** Can access the Filament Admin Panel (`/admin`). Can create, edit, and publish Master Data (Questionnaires, Categories, Sections). Can oversee all global Projects and Users.
- **Regular User:** Cannot access `/admin`. Operates entirely within the Inertia.js frontend.

## 2. Project-Scoped Roles

Authorization within the main application is heavily isolated by Project. A user can be an `owner` in Project A, an `auditor` in Project B, and have no access to Project C. 

The `project_members` pivot table maps `user_id`, `project_id`, and `role`.

### 2.1 Enforced Policies (`app/Policies/ProjectPolicy.php`)
- **`view`:** User must exist in the `project_members` table.
- **`create`:** Any authenticated user can create a new project.
- **`update`:** User must have the `'owner'` role for that specific project.
- **`delete`:** User must have the `'owner'` role.
- **`invite`:** User must have the `'owner'` role.

### 2.2 Roles Inference
Based on the `ProjectPolicy` and common domain logic, the standard project roles are:
- **Owner:** Full control. Can edit project settings, manage members, close rounds, and manage publications/badges.
- **Reviewer / Admin (implied):** Can review divergences and consolidate responses, but may not be able to delete the project.
- **Responder / Member:** Can execute an Inspection assigned to them and answer questions.

## 3. Publication & Visibility Policies

Additional policies guard the exposure of data:
- **`RoundPublicationPolicy.php` / `RoundBadgePolicy.php`:** Ensure that only authorized members (likely Owners) of the parent Project can generate public links, badges, or directory entries for an Evaluation Round.
- **Visibility Enum (`app/Enums/Visibility.php`):** Used at the data layer to dictate if a generated publication is truly public or restricted.

---
**Confidence Level:** ★★★★★ (Directly extracted from `app/Policies/ProjectPolicy.php` and `project_members` migration).
