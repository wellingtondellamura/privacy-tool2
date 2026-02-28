Feature: Evaluation Round Creation
  In order to organize inspections into semantic cycles
  As a project owner
  I want to create evaluation rounds

  Scenario: Owner creates evaluation round
    Given an existing project
    And an authenticated owner
    When the owner creates a new evaluation round with name "Avaliação Inicial"
    Then the round must be persisted
    And its status must be "draft"
    And diagnosis must be empty
    And no snapshot must exist

  Scenario: Non-owner cannot create round
    Given an existing project
    And an authenticated user who is not owner
    When attempting to create a new evaluation round
    Then the system must return 403 Forbidden