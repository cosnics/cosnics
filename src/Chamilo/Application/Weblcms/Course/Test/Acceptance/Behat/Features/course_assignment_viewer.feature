@user @courses
Feature: application.weblcms.course.assignment_viewer
  In order to view the assignment tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Assignment is accessible
    Given I am logged in
    When I go to the tool "assignment" in the course "Testcourse 1"
    Then The page should be successfully loaded
