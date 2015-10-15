@user @courses
Feature: application.weblcms.course.learning_path_viewer
  In order to view the learning_path tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Learning Path is accessible
    Given I am logged in
    When I go to the tool "learning_path" in the course "Testcourse 1"
    Then The page should be successfully loaded
