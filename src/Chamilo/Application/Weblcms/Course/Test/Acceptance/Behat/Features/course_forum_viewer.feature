@user @courses
Feature: application.weblcms.course.forum_viewer
  In order to view the forum tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Forum is accessible
    Given I am logged in
    When I go to the tool "forum" in the course "Testcourse 1"
    Then The page should be successfully loaded
