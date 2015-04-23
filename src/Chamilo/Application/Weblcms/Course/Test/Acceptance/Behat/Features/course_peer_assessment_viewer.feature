@user @courses
Feature: application.weblcms.course.peer_assessment_viewer
  In order to view the peer_assessment tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Peer Assessment is accessible
    Given I am logged in
    When I go to the tool "peer_assessment" in the course "Testcourse 1"
    Then The page should be successfully loaded
