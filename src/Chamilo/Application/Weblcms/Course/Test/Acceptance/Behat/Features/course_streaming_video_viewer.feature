@user @courses
Feature: application.weblcms.course.streaming_video_viewer
  In order to view the streaming_video tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Streaming Video is accessible
    Given I am logged in
    When I go to the tool "streaming_video" in the course "Testcourse 1"
    Then The page should be successfully loaded
