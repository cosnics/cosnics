@user @courses
Feature: application.weblcms.course.course_group_viewer
  In order to view the course_group tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Course Group is accessible
    Given I am logged in
    When I go to the tool "course_group" in the course "Testcourse 1"
    Then The page should be successfully loaded
