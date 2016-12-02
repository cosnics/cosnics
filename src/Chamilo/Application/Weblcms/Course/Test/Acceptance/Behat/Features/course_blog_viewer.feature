@user @courses
Feature: application.weblcms.course.blog_viewer
  In order to view the blog tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Blog is accessible
    Given I am logged in
    When I go to the tool "blog" in the course "Testcourse 1"
    Then The page should be successfully loaded
