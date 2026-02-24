Feature: Inspection State Transitions
  In order to preserve workflow integrity
  As the system
  I must enforce valid transitions

  Scenario: Valid transitions
    Given an inspection in draft status
    When activating the inspection
    Then status must become active

    Given an inspection in active status
    When closing the inspection
    Then status must become closed

  Scenario: Invalid transition
    Given an inspection in closed status
    When attempting to reopen
    Then the system must reject the operation
