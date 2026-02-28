Feature: Public Directory of Evaluation Rounds
  In order to browse evaluated tools
  As a visitor
  I want to see public rounds

  Scenario: List public rounds
    Given multiple rounds with mixed visibility
    When accessing /tools
    Then only rounds with visibility not equal to "private" must be listed

  Scenario: Access consolidated_only round
    Given a round with visibility "consolidated_only"
    When accessing its public page
    Then only consolidated score and diagnosis must be visible
    And no individual data must be visible

  Scenario: Access consolidated_and_individual round
    Given a round with visibility "consolidated_and_individual"
    When accessing its public page
    Then consolidated data must be visible
    And anonymized individual scores must be visible
    And no user identifiers must be present

  Scenario: Access private round
    Given a round with visibility "private"
    When accessing its public page
    Then the system must return 404