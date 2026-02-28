Feature: Migration of Existing Inspections to Evaluation Rounds
  In order to preserve existing system integrity
  As the system
  I must migrate inspections safely

  Scenario: Automatic round creation for existing projects
    Given a project with inspections but no evaluation rounds
    When running migration
    Then a default round named "Rodada Inicial" must be created
    And existing inspections must be linked to that round
    And a consolidated snapshot must be generated without recalculation

  Scenario: Migration must not alter scores
    Given existing snapshots before migration
    When migration completes
    Then all scores must remain unchanged