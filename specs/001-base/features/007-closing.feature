Feature: Closing Inspection
  In order to finalize evaluation
  As a project owner
  I want to close inspections

  Scenario: Close inspection
    Given an active inspection with responses
    And an authenticated owner
    When the owner closes the inspection
    Then individual snapshots must be generated
    And a consolidated snapshot must be generated
    And the inspection status must be set to closed

  Scenario: Snapshot immutability
    Given a closed inspection
    When underlying responses are modified directly in storage
    Then the stored snapshot must remain unchanged
