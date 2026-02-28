Feature: Closing Evaluation Round
  In order to consolidate a cycle of inspections
  As a project owner
  I want to close an evaluation round

  Scenario: Close round with at least one completed inspection
    Given a draft evaluation round
    And at least one inspection with status closed
    And an authenticated owner
    When the owner closes the round with diagnosis "Produto apresenta falhas estruturais."
    Then a consolidated snapshot must be generated
    And the round status must become "closed"
    And closed_at must be set

  Scenario: Cannot close round without inspections
    Given a draft evaluation round
    And no inspections exist
    When attempting to close the round
    Then the system must reject the operation

  Scenario: Cannot close round as non-owner
    Given a draft evaluation round
    And an authenticated evaluator
    When attempting to close the round
    Then the system must return 403 Forbidden

  Scenario: Inspections become immutable after closing round
    Given a closed evaluation round
    When attempting to modify an inspection
    Then the system must reject the operation