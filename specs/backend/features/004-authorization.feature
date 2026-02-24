Feature: Authorization Rules
  In order to protect project integrity
  As the system
  I must enforce role-based access

  Scenario: Only owner can close inspection
    Given an active inspection
    And a user with role evaluator
    When attempting to close inspection
    Then the system must return 403 Forbidden

  Scenario: Observer can view closed inspection
    Given a closed inspection
    And a user with role observer
    When accessing results
    Then the user must receive consolidated snapshot
