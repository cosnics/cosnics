@user @courses
Feature: application.weblcms.course.document_viewer
  In order to view the document tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Document is accessible
    Given I am logged in
    When I go to the tool "document" in the course "Testcourse 1"
    Then The page should be successfully loaded
