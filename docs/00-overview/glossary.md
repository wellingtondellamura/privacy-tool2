# Glossary

This document defines the core terminology used throughout the Mitra Privacy Tool source code and specifications. Understanding these terms is essential for comprehending the domain model and business rules.

## Core Terms

- **Project (`Project`)**
  The top-level organizational unit. It represents a specific compliance initiative within an organization. It contains team members and groups of privacy inspections.

- **Member (`User` / Role)**
  A user who has been granted access to a specific Project. Members can have different roles (e.g., owner, admin, auditor) which dictate their permissions.

- **Invitation (`Invitation`)**
  A time-limited secure token sent via email allowing external or existing users to join a Project.

- **Inspection (`Inspection`)**
  An assessment instance where users answer questionnaires regarding privacy practices. Inspections belong to a Project and form the basis of compliance tracking.

- **Evaluation Round (`EvaluationRound`)**
  A grouped phase of inspections. Instead of just answering questions linearly, inspections are managed in "rounds" which have different states (open, review, consolidation, closed). This allows for iterative assessments over time.

- **Response (`Response`)**
  An individual answer provided by a user to a specific question during an Inspection.

- **Consolidated Response (`ConsolidatedResponse`)**
  In team-based or multi-reviewer rounds, individual responses may be reviewed and merged into a single "Consolidated Response" representing the final organizational stance on that question.

- **Review Comment (`ReviewComment`)**
  Feedback or discussion attached to specific answers or sections during the review phase of an Evaluation Round.

- **Result (`Result`)**
  The computed outcome of an Inspection or Evaluation Round. It often includes scores, metrics, and comparisons against other periods or standard benchmarks.

- **Badge (`RoundBadge`)**
  An embeddable visual asset (usually an HTML snippet/JS script) that organizations can place on their public websites to demonstrate their compliance status or inspection results for a specific Evaluation Round.

- **Public Directory (`PublicDirectory`)**
  A public-facing section of the platform where authorized results or tools can be listed and searched by external visitors.

---
**Confidence Level:** ★★★★☆ (Inferred from controller names and routes, to be detailed in Phase 3 Domain Model).
