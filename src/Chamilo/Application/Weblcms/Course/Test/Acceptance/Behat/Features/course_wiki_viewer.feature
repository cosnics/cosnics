@user @courses
Feature: application.weblcms.course.wiki_viewer
  In order to view the wiki tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Wiki is accessible
    Given I am logged in
    When I go to the tool "wiki" in the course "Testcourse 1"
    Then The page should be successfully loaded
