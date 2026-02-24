Feature: Inspection Comparison
  In order to measure improvement
  As a project owner
  I want to compare inspections

  Scenario: Positive delta
    Given inspection A with score 60
    And inspection B with score 80
    When comparing B to A
    Then delta must be +20

  Scenario: Negative delta
    Given inspection A with score 80
    And inspection B with score 60
    When comparing B to A
    Then delta must be -20

  Scenario: Invalid comparison
    Given two inspections from different projects
    When attempting comparison
    Then the system must return 400 Bad Request
