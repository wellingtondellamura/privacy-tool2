Feature: Public Directory
  In order to browse evaluated tools
  As a visitor
  I want to see public inspection results

  Scenario: List public tools
    Given multiple inspections with mixed visibility
    When accessing /tools
    Then only inspections with visibility not equal to "private" must be listed

  Scenario: Access score_public inspection
    Given an inspection with visibility "score_public"
    When accessing its public page
    Then only summary data must be visible
    And detailed report must not be visible

  Scenario: Access full_public inspection
    Given an inspection with visibility "full_public"
    When accessing its public page
    Then the complete consolidated report must be visible
    And no individual responses must be visible

  Scenario: Access private inspection
    Given an inspection with visibility "private"
    When accessing its public page
    Then the system must return 404
