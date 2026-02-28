Feature: Inspection Publication
  In order to make inspection results public
  As a project owner
  I want to control publication visibility

  Scenario: Publish closed inspection
    Given an inspection with status closed
    And the authenticated user is the project owner
    When the user publishes the inspection with visibility "score_public"
    Then a publication record must be created
    And published_at must be set
    And the inspection must appear in the public directory

  Scenario: Cannot publish active inspection
    Given an inspection with status active
    When the owner attempts to publish
    Then the system must reject the operation

  Scenario: Non-owner cannot publish
    Given a closed inspection
    And the authenticated user is not owner
    When attempting to publish
    Then the system must return 403 Forbidden

  Scenario: Change visibility
    Given a published inspection with visibility "score_public"
    When the owner updates visibility to "full_public"
    Then the publication must reflect the new visibility
    And snapshot must remain unchanged

  Scenario: Revoke publication
    Given a published inspection
    When the owner revokes publication
    Then visibility must become "private"
    And the inspection must not appear in the public directory
