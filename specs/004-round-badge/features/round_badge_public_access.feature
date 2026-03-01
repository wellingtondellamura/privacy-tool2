Feature: Public Badge Access
  In order to embed official results
  As a visitor
  I want to access badge endpoints

  Scenario: Access active badge
    Given an active badge
    When accessing /badge/{token}
    Then the system must return badge JSON
    And it must include score and medal
    And it must not include evaluator identities

  Scenario: Access inactive badge
    Given a revoked badge
    When accessing /badge/{token}
    Then the system must return 404

  Scenario: Access badge for private round
    Given a badge linked to a round with visibility "private"
    When accessing /badge/{token}
    Then the system must return 404