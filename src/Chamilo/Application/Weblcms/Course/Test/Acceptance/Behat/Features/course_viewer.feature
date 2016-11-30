@user @courses
Feature: application.weblcms.course.course_viewer
  In order to view the course
  As a user
  The course needs to be accessible

  Scenario: Check if the course is available
    Given I am logged in
    When I go to the course "Testcourse 1"
    Then The page should be successfully loaded
    And I should see "Testcourse 1"
