Feature: Inspections Within Evaluation Round
  In order to evaluate a tool in a specific cycle
  As a project member
  I want inspections to belong to a round

  Scenario: Create inspection inside draft round
    Given a project with a draft evaluation round
    And an authenticated evaluator
    When creating a new inspection
    Then the inspection must reference the evaluation round

  Scenario: Cannot create inspection in closed round
    Given a project with a closed evaluation round
    When attempting to create a new inspection
    Then the system must reject the operation

  Scenario: Member participates in multiple rounds
    Given a project with two draft rounds
    And an authenticated evaluator
    When creating inspections in both rounds
    Then both inspections must be valid