Feature: Badge Revocation
  In order to control external display
  As a project owner
  I want to revoke badge access

  Scenario: Revoke badge
    Given an active badge
    When the owner revokes the badge
    Then is_active must be false
    And accessing the badge endpoint must return 404

  Scenario: Generate new badge after revocation
    Given a revoked badge
    When the owner generates a new badge
    Then a new token must be created
    And the old token must remain invalid