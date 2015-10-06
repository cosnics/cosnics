@user @courses
Feature: application.weblcms.course.calendar_viewer
  In order to view the calendar tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Calendar is accessible
    Given I am logged in
    When I go to the tool "calendar" in the course "Testcourse 1"
    Then The page should be successfully loaded
