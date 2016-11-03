@user @courses
Feature: application.weblcms.course.search_viewer
  In order to view the search tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Search is accessible
    Given I am logged in
    When I go to the tool "search" in the course "Testcourse 1"
    Then The page should be successfully loaded
