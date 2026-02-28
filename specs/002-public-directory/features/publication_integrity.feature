Feature: Publication Integrity
  In order to preserve determinism
  As the system
  I must ensure publication does not alter results

  Scenario: Snapshot remains immutable
    Given a published inspection
    When underlying responses are modified in storage
    Then the public score must remain equal to the snapshot value

  Scenario: No recalculation on publication
    Given a closed inspection
    When publishing
    Then no recalculation of score must occur
    And the existing snapshot must be used
