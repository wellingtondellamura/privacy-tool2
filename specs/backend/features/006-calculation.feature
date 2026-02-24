Feature: Deterministic Calculation
  In order to ensure reproducibility
  As the system
  I must calculate scores deterministically

  Scenario: Score mapping
    Given an answer "Sufficient"
    When converted to numeric score
    Then the score must be 100

    Given an answer "Insufficient"
    Then the score must be 50

    Given an answer "Inexistent"
    Then the score must be 0

  Scenario: Category score calculation
    Given a category with 4 questions
    And total score 300
    When calculating category score
    Then the result must be round((300 / 400) * 100)

  Scenario: Section score calculation
    Given category scores 80 and 60
    When calculating section score
    Then the result must be 70

  Scenario: Medal assignment
    Given a section score of 95
    Then the medal must be "Ouro"

    Given a section score of 70
    Then the medal must be "Prata"

    Given a section score of 50
    Then the medal must be "Bronze"

    Given a section score of 30
    Then the medal must be "Incipiente"
