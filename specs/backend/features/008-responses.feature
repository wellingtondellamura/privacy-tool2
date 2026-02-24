Feature: Inspection Responses
  In order to evaluate transparency
  As an evaluator
  I want to submit responses

  Scenario: Save valid response
    Given an active inspection
    And a user with role evaluator
    When the user submits a valid answer to a question
    Then the response should be stored
    And any previous answer should be replaced

  Scenario: Observer cannot respond
    Given an active inspection
    And a user with role observer
    When the user attempts to submit a response
    Then the system should return 403 Forbidden

  Scenario: Cannot respond after closing
    Given a closed inspection
    When an evaluator attempts to submit a response
    Then the system should reject the operation
