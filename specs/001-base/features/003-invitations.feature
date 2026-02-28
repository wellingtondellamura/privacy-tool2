Feature: Project Invitations
  In order to collaborate
  As a project owner
  I want to invite new members

  Scenario: Invite new member
    Given an authenticated owner of a project
    When the owner invites a valid email
    Then a unique invitation token should be generated
    And the invitation should be stored
    And an email should be sent

  Scenario: Accept invitation with new account
    Given a valid invitation exists
    And the invited email has no existing account
    When the invitation token is accepted
    Then a new user account should be created
    And the user should be added to the project

  Scenario: Accept expired invitation
    Given an expired invitation
    When the invitation token is used
    Then the system should return an invitation expired error
