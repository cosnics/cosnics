@user @courses
Feature: application.weblcms.course.chat_viewer
  In order to view the chat tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Chat is accessible
    Given I am logged in
    When I go to the tool "chat" in the course "Testcourse 1"
    Then The page should be successfully loaded
