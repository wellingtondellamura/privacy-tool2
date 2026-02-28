Feature: Evaluation Round Comparison
  In order to analyze historical evolution
  As a project owner
  I want to compare multiple rounds

  Scenario: Compare two rounds
    Given a project with two closed evaluation rounds
    When comparing round A with round B
    Then delta by section must be calculated
    And delta by category must be calculated
    And global delta must be calculated

  Scenario: Compare multiple rounds (historical series)
    Given a project with three closed rounds
    When requesting historical comparison
    Then the system must return ordered time series
    And each round must use its stored snapshot
    And no recalculation must occur

  Scenario: Cannot compare rounds from different projects
    Given two rounds from different projects
    When attempting comparison
    Then the system must return 400 Bad Request