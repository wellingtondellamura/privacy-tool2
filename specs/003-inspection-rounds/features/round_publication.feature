Feature: Evaluation Round Publication
  In order to make results public
  As a project owner
  I want to control publication visibility

  Scenario: Publish closed round
    Given a closed evaluation round
    And an authenticated owner
    When publishing with visibility "consolidated_only"
    Then visibility must be set
    And published_at must be set
    And the round must appear in public directory

  Scenario: Cannot publish draft round
    Given a draft evaluation round
    When attempting to publish
    Then the system must reject the operation

  Scenario: Publish with individual data
    Given a closed evaluation round
    When publishing with visibility "consolidated_and_individual"
    Then consolidated data must be public
    And anonymized individual scores must be public
    And evaluator identities must not be exposed

  Scenario: Revoke publication
    Given a published evaluation round
    When changing visibility to "private"
    Then the round must not appear in public directory

  Scenario: Diagnosis cannot change after publication
    Given a published evaluation round
    When attempting to edit diagnosis
    Then the system must reject the operation