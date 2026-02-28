Feature: User Authentication
  In order to access the platform
  As a visitor or registered user
  I want to register and authenticate securely

  Scenario: Successful user registration
    Given a visitor is not authenticated
    When the visitor submits valid name, email and password
    Then a new user record should be created
    And the email should be marked as unverified
    And a verification email should be sent

  Scenario: Successful login
    Given a registered user with verified email exists
    When the user submits valid credentials
    Then the user should be authenticated
    And a valid session should be created
