# User Stories: Evaluation Rounds & Consensus

This document contains BDD/Gherkin scenarios detailing how Evaluation Rounds are managed, reviewed, and closed.

## US-03: Create an Evaluation Round

**As a** Project Admin
**I want to** start a new Evaluation Round
**So that** I can gather a fresh set of privacy inspections from my team for a specific period or system release.

### Acceptance Criteria
- The admin must define a start date, end date, and the software version being evaluated.
- The system must link the Round to the active Project.
- The Round's initial status must be `open`.

### BDD Scenarios

```gherkin
Feature: Evaluation Round Initialization

  Scenario: Starting a new round
    Given I am a project admin
    When I navigate to the "Rounds" section
    And I submit a new round with start date "2026-08-01"
    Then the system creates an EvaluationRound record
    And the round status is set to "open"
```

---

## US-04: Review and Consolidate Responses

**As a** Project Admin or Reviewer
**I want to** review the diverging answers given by my team during an Evaluation Round
**So that** I can establish a single, consolidated organizational answer.

### Acceptance Criteria
- The round must be transitioned to the `review` state.
- Reviewers can leave comments on specific questions.
- Reviewers can manually override or consolidate the final answer if the `ConsensusModel` requires it.

### BDD Scenarios

```gherkin
Feature: Response Review and Consolidation

  Scenario: Adding a review comment to a diverging question
    Given the Evaluation Round is in the "review" state
    And there is a high divergence on question ID 42
    When I add a Review Comment "We need to align with Legal on this."
    Then the comment is saved and visible to other reviewers

  Scenario: Consolidating an answer
    Given the project uses the "MANUAL_REVIEW" consensus model
    When I select "Fully Implemented" as the final answer for question ID 42
    Then the system saves this in the "consolidated_responses" table
```

---

## US-05: Close an Evaluation Round

**As a** Project Admin
**I want to** close an Evaluation Round
**So that** the final scores are calculated and locked immutably.

### Acceptance Criteria
- All linked inspections must be in the `closed` state.
- The system must generate a `RoundSnapshot` (as per BR-003).

### BDD Scenarios

```gherkin
Feature: Round Closure

  Scenario: Successfully closing a round
    Given all inspections in the round are closed
    When I click "Close Round"
    Then the system calculates the final aggregated scores
    And creates a "RoundSnapshot" with the JSON payload
    And changes the round status to "closed"
```

---
**Confidence Level:** ★★★★★ (Derived from `EvaluationRoundController` and `CloseRoundAction.php`).
