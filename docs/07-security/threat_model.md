# Threat Model (STRIDE)

This document provides a threat model for the Mitra Privacy Tool, utilizing the STRIDE methodology to identify potential security risks and document the mitigating controls in place.

## 1. Spoofing Identity
*Threat:* An attacker attempts to act as another user or system administrator.
*Mitigation:* 
- **Authentication:** Managed by Laravel Sanctum. Session hijacking is mitigated via secure, HttpOnly, SameSite cookies.
- **Passwords:** Encrypted using Bcrypt with 12 rounds.
- **Invitations:** Tokens are cryptographically random and time-limited, preventing brute-forcing of invitation links.

## 2. Tampering with Data
*Threat:* An attacker attempts to alter inspection responses or evaluation round scores.
*Mitigation:*
- **Immutability:** Once an inspection or round is `closed`, the system generates a Snapshot (`ResultSnapshot`, `RoundSnapshot`). Any subsequent API call to alter a response on a closed inspection is blocked at the Controller/Action level.
- **CSRF Protection:** Laravel's standard CSRF tokens are injected into every Inertia.js request, preventing Cross-Site Request Forgery.
- **Mass Assignment:** Eloquent `$fillable` properties prevent arbitrary injection of fields (e.g., trying to elevate a role during a profile update).

## 3. Repudiation
*Threat:* A user denies making a specific response or review comment.
*Mitigation:*
- **Attribution:** Every `Response` and `ReviewComment` is strictly tied to a `user_id`.
- **Soft Deletes:** Key models are soft-deleted, leaving an audit trail in the database even if a user attempts to "delete" a project or inspection.

## 4. Information Disclosure
*Threat:* An attacker attempts to read privacy assessments of another project.
*Mitigation:*
- **Authorization:** `ProjectPolicy` strictly checks if `$project->hasMember($user)`.
- **Tenant Isolation:** All data queries in controllers are scoped to the active project or the authenticated user (`$request->user()->projects()`). 
- **Public Directory:** Only rounds explicitly authorized via `RoundPublication` are exposed to unauthenticated endpoints.

## 5. Denial of Service
*Threat:* An attacker attempts to overwhelm the server or database.
*Mitigation:*
- **Rate Limiting:** Laravel's default `RateLimiter` applies to authentication attempts and can be applied to API routes.
- **Pagination:** Results and lists use pagination to prevent heavy database loads.

## 6. Elevation of Privilege
*Threat:* A regular user attempts to access the Filament admin panel or perform a Project Owner action.
*Mitigation:*
- **Filament Access:** The `is_admin` boolean on the `User` model gates access to the `/admin` routes.
- **Project Roles:** `ProjectPolicy` explicitly checks for the `'owner'` role before allowing destructive actions or member management.

---
**Confidence Level:** ★★★★☆ (Inferred based on standard Laravel security configurations and the analyzed Policies).
