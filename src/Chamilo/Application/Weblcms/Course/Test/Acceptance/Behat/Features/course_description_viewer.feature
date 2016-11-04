@user @courses
Feature: application.weblcms.course.description_viewer
  In order to view the description tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Description is accessible
    Given I am logged in
    When I go to the tool "description" in the course "Testcourse 1"
    Then The page should be successfully loaded
