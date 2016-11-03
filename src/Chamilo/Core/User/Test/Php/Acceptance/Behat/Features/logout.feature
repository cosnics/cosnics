Feature: user.logout
  In order to get out of chamilo
  As a logged in user
  I need to be able to logout of chamilo

  Scenario: Logout as a logged in user
    Given I am on "/index.php"
    And I am logged in
    When I follow "Logout"
    Then I should see "Login"
