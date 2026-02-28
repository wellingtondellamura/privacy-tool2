Feature: Round Integrity and Determinism
  In order to preserve mathematical integrity
  As the system
  I must ensure snapshots remain immutable

  Scenario: Snapshot remains immutable after publication
    Given a published round
    When underlying inspection responses are modified directly in storage
    Then the public consolidated score must remain equal to stored snapshot

  Scenario: No recalculation during publication
    Given a closed round
    When publishing
    Then no score recalculation must occur
    And stored snapshot must be used

  Scenario: No recalculation during comparison
    Given two closed rounds
    When comparing
    Then comparison must use stored snapshots
    And no recalculation must occur