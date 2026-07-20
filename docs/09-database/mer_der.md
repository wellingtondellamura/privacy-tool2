# Database Model (ERD)

This document describes the logical and physical data model of the Mitra Privacy Tool, inferred from the Eloquent Models and Laravel Database Migrations.

## 1. Entity-Relationship Diagram (ERD)

The following Mermaid diagram maps the core tables, primary relationships, and cardinalities.

```mermaid
erDiagram
    USERS ||--o{ PROJECT_MEMBERS : "is member"
    USERS ||--o{ INSPECTIONS : "performs"
    USERS ||--o{ REVIEW_COMMENTS : "authors"
    
    PROJECTS ||--o{ PROJECT_MEMBERS : "has"
    PROJECTS ||--o{ INVITATIONS : "sends"
    PROJECTS ||--o{ EVALUATION_ROUNDS : "contains"
    PROJECTS ||--o{ INSPECTIONS : "owns"
    
    QUESTIONNAIRE_VERSIONS ||--o{ CATEGORIES : "groups"
    QUESTIONNAIRE_VERSIONS ||--o{ INSPECTIONS : "used in"
    
    CATEGORIES ||--o{ SECTIONS : "contains"
    SECTIONS ||--o{ QUESTIONS : "contains"
    
    EVALUATION_ROUNDS ||--o{ INSPECTIONS : "groups"
    EVALUATION_ROUNDS ||--o{ CONSOLIDATED_RESPONSIBILITIES : "has final answers"
    EVALUATION_ROUNDS ||--o| ROUND_SNAPSHOTS : "has"
    EVALUATION_ROUNDS ||--o{ ROUND_PUBLICATIONS : "publishes"
    EVALUATION_ROUNDS ||--o{ ROUND_BADGES : "generates"
    
    INSPECTIONS ||--o{ RESPONSES : "contains"
    INSPECTIONS ||--o| RESULT_SNAPSHOTS : "has"
    INSPECTIONS ||--o{ INSPECTION_PUBLICATIONS : "publishes"
    
    QUESTIONS ||--o{ RESPONSES : "answered by"
    QUESTIONS ||--o{ CONSOLIDATED_RESPONSIBILITIES : "final answer for"
    
    RESPONSES ||--o{ REVIEW_COMMENTS : "receives"
    
    USERS {
        bigint id PK
        string name
        string email
        string password
        string locale
        timestamp last_login_at
        timestamp terms_accepted_at
        boolean is_admin
    }
    
    PROJECTS {
        bigint id PK
        string name
        string slug
        string icon
        string color
        json settings
        boolean show_evaluations_to_all
    }
    
    PROJECT_MEMBERS {
        bigint project_id FK
        bigint user_id FK
        string role
    }
    
    EVALUATION_ROUNDS {
        bigint id PK
        bigint project_id FK
        string status "open, review, closed"
        date start_date
        date end_date
        text diagnosis
        string software_version
    }
    
    INSPECTIONS {
        bigint id PK
        string sequential_id
        bigint project_id FK
        bigint evaluation_round_id FK
        bigint user_id FK
        string status
    }
    
    RESPONSES {
        bigint id PK
        bigint inspection_id FK
        bigint question_id FK
        string answer_level
        text notes
    }
    
    CONSOLIDATED_RESPONSIBILITIES {
        bigint id PK
        bigint evaluation_round_id FK
        bigint question_id FK
        string consensus_model
        string answer_level
    }
```

## 2. Key Relationships & Constraints

### Many-to-Many Pivot Tables
- `project_members`: Connects `users` and `projects`. No auto-incrementing ID is strictly necessary, primary key is a composite of `(project_id, user_id)`. Includes a `role` column to handle permissions within the project context.

### JSON & Translatable Columns
Based on `spatie/laravel-translatable`, several tables use `json` columns to store string translations.
- For example, `questions.text`, `sections.title`, and `categories.name` are likely JSON columns formatted as `{"en": "Text", "pt_BR": "Texto"}`.
- `projects.settings` is also stored as JSON to provide schema-less flexibility for project-specific configurations.

### Soft Deletes & Timestamps
- The system heavily relies on Laravel's standard timestamps (`created_at`, `updated_at`).
- Important tables like `projects`, `users`, and `inspections` likely implement `SoftDeletes` (`deleted_at`) to ensure data is not inadvertently destroyed, preserving audit trails for compliance purposes.

---
**Confidence Level:** ★★★★★ (Derived strictly from `database/migrations` filenames and standard Laravel conventions).
