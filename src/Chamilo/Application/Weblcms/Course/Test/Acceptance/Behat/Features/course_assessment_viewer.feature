@user @courses
Feature: application.weblcms.course.assessment_viewer
  In order to view the assessment tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Assessment is accessible
    Given I am logged in
    When I go to the tool "assessment" in the course "Testcourse 1"
    Then The page should be successfully loaded
