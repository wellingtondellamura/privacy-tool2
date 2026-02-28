Feature: Project Management
  In order to organize inspections
  As an authenticated user
  I want to create and manage projects

  Scenario: Create project
    Given an authenticated user
    When the user creates a project with valid name and URL
    Then the project should be persisted
    And the user should be assigned as owner
    And the project should have exactly one owner

  Scenario: Unauthorized access to project
    Given a user who is not a member of a project
    When the user attempts to access the project
    Then the system should return 403 Forbidden
