# System Overview

## 1. Description
The **Mitra Privacy Tool** is a robust platform designed to support organizations in privacy management, compliance, and data protection. The tool centralizes the management of privacy inspections, compliance projects, and team collaboration within a single intuitive environment.

## 2. Key Modules & Scope
Based on the code analysis (specifically routes and folder structures), the core modules of the system include:

1. **Projects & Team Collaboration:** 
   - Creation and management of compliance projects.
   - Team invitations (with token acceptance), role management, and access control.
2. **Inspections & Evaluation Rounds:** 
   - Execution of privacy inspections.
   - Grouping assessments in "Evaluation Rounds" (`EvaluationRoundController`).
   - Round states: open, review, close, consolidation (`ConsolidatedResponseController`).
3. **Questionnaires & Responses:** 
   - Gathering responses for inspections.
   - Review processes and comments (`ReviewCommentController`).
4. **Results & Analytics:** 
   - Individual, team, and comparative results (`ResultController`).
   - Historical snapshots and reporting.
5. **Public Directory & Badges:**
   - Public exposure of privacy tools or results (`PublicDirectoryController`).
   - Embeddable badges for websites (`RoundBadgeController`).
6. **Data Export:**
   - Capabilities to export project and profile data for portability (`DataExportController`).
7. **Multi-language Support:**
   - Internationalization capabilities (pt_BR, en, es) handled via session locales and Spatie translatable models.

## 3. Technology Stack

### Backend
- **Framework:** Laravel v12
- **Language:** PHP 8.2+
- **Admin Panel:** Filament v4.0
- **Translation:** Spatie Laravel Translatable
- **Database:** Relational (MySQL / PostgreSQL / SQLite)

### Frontend
- **Framework:** Vue.js 3
- **Integration:** Inertia.js (Laravel adapter)
- **Styling:** Tailwind CSS (with `@tailwindcss/vite` and Forms plugin)
- **Tooling:** Vite, PostCSS, Autoprefixer

### Testing & Tooling
- **Unit/Feature Testing:** Pest PHP v4.4
- **E2E Testing:** Playwright
- **Static Analysis/Quality:** Laravel Pint

## 4. Architectural Patterns
The application follows a monolithic MVC (Model-View-Controller) architecture enriched by modern tooling:
- **Client-Server Communication:** Inertia.js acts as the glue, allowing standard Laravel controllers to return Vue components instead of Blade views without building separate REST APIs.
- **Admin Interface:** Filament provides a separate administrative panel built on top of Laravel Livewire and Alpine.js.
- **Service & Action Layers:** The presence of `app/Services`, `app/Actions`, and `app/Strategies` indicates the extraction of business logic from controllers into dedicated, reusable classes.
- **Authorization:** Standard Laravel Policies (`app/Policies`).

---
**Confidence Level:** ★★★★★ (Confirmed by `composer.json`, `package.json`, and `routes/web.php`)
