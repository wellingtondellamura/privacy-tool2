Feature: Badge Integrity
  In order to preserve determinism
  As the system
  I must ensure badge does not alter historical data

  Scenario: Badge uses snapshot only
    Given a published round with snapshot
    When accessing badge endpoint
    Then the response must match the stored snapshot score
    And no recalculation must occur

  Scenario: Snapshot modification does not affect badge
    Given a published round
    When underlying responses are modified in storage
    Then the badge score must remain equal to stored snapshot