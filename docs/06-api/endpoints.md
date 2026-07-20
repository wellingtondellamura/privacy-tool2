# API / Internal Endpoints

Since the Mitra Privacy Tool is built using Inertia.js, it does not expose a traditional public REST API for the frontend. Instead, it relies on standard HTTP endpoints that return Inertia (JSON) responses to Vue, or standard redirects.

This document maps the primary endpoints based on `routes/web.php`.

## 1. Authentication & Profile
- `GET /login`, `/register`, `/password/reset`: Managed by Laravel Breeze.
- `GET /profile`: Edit profile data.
- `PATCH /profile`: Update profile fields.
- `PATCH /profile/locale`: Update user UI language preference.
- `POST /locale`: General language switcher for visitors.

## 2. Project Management
- `GET /projects`: List all projects for the authenticated user.
- `POST /projects`: Create a new project.
- `GET /projects/{project}`: View project dashboard.
- `PUT /projects/{project}/settings`: Update project settings (`show_evaluations_to_all`, etc.).
- `PUT /projects/{project}/members/{user}`: Update a member's role.

## 3. Invitations
- `POST /projects/{project}/invite`: Send an invitation token via email.
- `POST /invitations/{token}/accept`: Process invitation acceptance.
- `POST /invitations/{invitation}/resend`: Resend email.
- `DELETE /invitations/{invitation}`: Revoke invitation.

## 4. Evaluation Rounds
- `POST /projects/{project}/rounds`: Initialize a new evaluation round.
- `GET /rounds/{round}`: View round details.
- `PUT /rounds/{round}`: Update round metadata (start/end date).
- `POST /rounds/{round}/close`: Close a round, triggering `CloseRoundAction`.
- `GET /rounds/{round}/review`: Enter the divergence review interface.
- `POST /rounds/{round}/enter-review`: Transition the round status to `review`.

## 5. Inspections & Responses
- `POST /projects/{project}/inspections`: Start a new individual inspection.
- `GET /inspections/{inspection}`: Load the questionnaire for answering.
- `POST /inspections/{inspection}/activate`: Start the timer/change status to active.
- `POST /inspections/{inspection}/close`: Finalize the inspection.
- `POST /inspections/{inspection}/response`: Save a single answer to a question.

## 6. Review Comments & Consolidation
- `POST /rounds/{round}/comments`: Add a review comment to a question.
- `DELETE /comments/{comment}`: Remove a comment.
- `POST /rounds/{round}/consolidate`: Save the final consensus answer for a question.
- `DELETE /rounds/{round}/consolidate/{questionId}`: Revert a consolidated answer.

## 7. Results & Analytics
- `GET /inspections/{inspection}/results`: View single inspection snapshot.
- `GET /inspections/{inspection}/team-results`: View aggregation of the team.
- `GET /inspections/{inspection}/comparison/{other}`: Compare two inspections.
- `GET /rounds/{round}/results`: View the `RoundSnapshot`.
- `GET /rounds/{round}/comparison/{other}`: Compare two rounds.

## 8. Public Directory & Publications
- `GET /tools`: Browse public directory.
- `GET /tools/{slug}`: View public result details.
- `POST /rounds/{round}/publish`: Publish a round result to the directory.
- `DELETE /rounds/{round}/publish`: Unpublish.

## 9. Badges
- `POST /rounds/{round}/badge`: Generate a badge token for external embedding.
- `GET /badge/{token}.js`: The public JS script for embedding.
- `GET /badge/{token}`: The public HTML visual component.
- `PUT /badges/{badge}/style`: Update badge visuals.

## 10. Data Export
- `GET /projects/{project}/export`: Download project data.
- `GET /profile/export-all`: Download all personal data.

---
**Confidence Level:** ★★★★★ (Directly extracted from `routes/web.php` and `app/Http/Controllers`).
