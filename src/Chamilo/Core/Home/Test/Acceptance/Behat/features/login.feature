Feature: home.login
  In order to access chamilo
  As a user
  I need to be able to login in chamilo

  Scenario: Login as a valid user
    Given I am on the homepage
    When I fill in "login" with "admin"
    And I fill in "password" with "admin"
    And I press "Login"
    Then I should see "Logout"

  Scenario: Login as an invalid user
    Given I am on the homepage
    When I fill in "login" with "admin"
    And I fill in "password" with "invalid_password"
    And I press "Login"
    Then I should not see "Logout"
