# User Stories: Project & Team Management

This document contains BDD/Gherkin scenarios for managing compliance projects and team invitations within the Mitra Privacy Tool.

## US-01: Create a New Project

**As a** registered user
**I want to** create a new Project
**So that** I can organize my privacy compliance inspections within a specific workspace.

### Acceptance Criteria
- The user must provide a valid Project Name.
- The system must automatically generate a unique slug for the project.
- The creator must be automatically assigned the `owner` role in `project_members`.

### BDD Scenarios

```gherkin
Feature: Project Creation

  Scenario: Successful project creation
    Given I am logged in as a registered user
    When I navigate to the "Projects" dashboard
    And I click "New Project"
    And I fill in "Project Name" with "Acme Corp Compliance"
    And I submit the form
    Then the system should create the project
    And I should be redirected to the "Acme Corp Compliance" dashboard
    And my role should be "owner"
```

---

## US-02: Invite a Team Member

**As a** project owner or admin
**I want to** invite other users to my project
**So that** they can collaborate on privacy inspections.

### Acceptance Criteria
- Only users with appropriate permissions (Policy check) can send invitations.
- The system must send an email with a secure, time-limited token.
- The invitee can be a new user (requires registration) or an existing user.

### BDD Scenarios

```gherkin
Feature: Team Invitations

  Scenario: Inviting a new user
    Given I am a project owner of "Acme Corp Compliance"
    When I navigate to the "Team" tab
    And I invite "john@example.com" with the role "auditor"
    Then the system creates a pending invitation record
    And an email containing a secure token is sent to "john@example.com"

  Scenario Outline: Invitee accepts the invitation
    Given an invitation was sent to "<email>"
    When the user clicks the invitation link with the token
    And the user authenticates (or registers) with "<email>"
    Then the user is added to the "project_members" table
    And their role is set to "<role>"
    And the invitation record is marked as accepted/deleted

    Examples:
      | email              | role     |
      | john@example.com   | auditor  |
      | alice@example.com  | admin    |
```

---
**Confidence Level:** ★★★★★ (Derived from `ProjectController` and `InvitationController` actions).
