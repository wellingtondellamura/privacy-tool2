Feature: Team Consolidation
  In order to analyze collaborative results
  As the system
  I must calculate team averages and divergence

  Scenario: Average section score
    Given two users with section scores 80 and 60
    When calculating team average
    Then the result must be 70

  Scenario: Low divergence
    Given all users answered 100 for a question
    When calculating variance
    Then divergence classification must be "low"

  Scenario: High divergence
    Given answers 0, 100, 0, 100
    When calculating variance
    Then divergence classification must be "high"
