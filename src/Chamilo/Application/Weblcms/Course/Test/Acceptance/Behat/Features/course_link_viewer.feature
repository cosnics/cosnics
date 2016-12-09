@user @courses
Feature: application.weblcms.course.link_viewer
  In order to view the link tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Link is accessible
    Given I am logged in
    When I go to the tool "link" in the course "Testcourse 1"
    Then The page should be successfully loaded
