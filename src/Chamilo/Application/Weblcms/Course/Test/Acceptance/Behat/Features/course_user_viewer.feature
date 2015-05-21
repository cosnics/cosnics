@user @courses
Feature: application.weblcms.course.user_viewer
  In order to view the user tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool User is accessible
    Given I am logged in
    When I go to the tool "user" in the course "Testcourse 1"
    Then The page should be successfully loaded
