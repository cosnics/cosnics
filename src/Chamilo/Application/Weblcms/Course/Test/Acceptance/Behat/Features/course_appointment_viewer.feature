@user @courses
Feature: application.weblcms.course.appointment_viewer
  In order to view the appointment tool
  As a user
  The tool in the course needs to be accessible

  Scenario: Check if the tool Appointment is accessible
    Given I am logged in
    When I go to the tool "appointment" in the course "Testcourse 1"
    Then The page should be successfully loaded
