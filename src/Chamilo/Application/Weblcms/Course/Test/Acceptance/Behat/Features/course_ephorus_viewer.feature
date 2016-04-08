@user @courses
Feature: application.weblcms.course.ephorus_viewer
  In order to view the ephorus tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Ephorus is accessible
    Given I am logged in
    When I go to the tool "ephorus" in the course "Testcourse 1"
    Then The page should be successfully loaded
