Feature: Round Badge Creation
  In order to display official evaluation results externally
  As a project owner
  I want to generate an embeddable badge

  Scenario: Create badge for published round
    Given a published evaluation round
    And visibility is not "private"
    When the owner generates a badge
    Then a unique public token must be created
    And the badge must be active

  Scenario: Cannot create badge for draft round
    Given a draft evaluation round
    When attempting to generate a badge
    Then the system must reject the operation

  Scenario: Non-owner cannot create badge
    Given a published evaluation round
    And a non-owner user
    When attempting to generate a badge
    Then the system must return 403 Forbidden