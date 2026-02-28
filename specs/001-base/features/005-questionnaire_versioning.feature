Feature: Questionnaire Versioning
  In order to preserve historical integrity
  As the system
  I must version questionnaires

  Scenario: New inspection uses active version
    Given an active questionnaire version exists
    When a new inspection is created
    Then the inspection must reference the active version

  Scenario: New version does not affect existing inspections
    Given an inspection referencing version 1
    And a new questionnaire version 2 is created
    When retrieving the existing inspection
    Then it must still reference version 1
