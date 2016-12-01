@user @courses
Feature: application.weblcms.course.note_viewer
  In order to view the note tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Note is accessible
    Given I am logged in
    When I go to the tool "note" in the course "Testcourse 1"
    Then The page should be successfully loaded
