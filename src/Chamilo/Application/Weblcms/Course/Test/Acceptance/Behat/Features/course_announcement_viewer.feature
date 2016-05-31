@user @courses
Feature: application.weblcms.course.announcement_viewer
  In order to view the announcement tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Announcement is accessible
    Given I am logged in
    When I go to the tool "announcement" in the course "Testcourse 1"
    Then The page should be successfully loaded
