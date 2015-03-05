Feature: chamilo.core.tracking.showapplication
  In order to use the application
  As an admin
  I should be able to load all pages

  Background:
    Given I am logged in

  Scenario: Show application
    When I go to application "Chamilo\Core\Tracking"

  Scenario: Show tracking list
    When I go to application "Chamilo\Core\Tracking" and do action "admin_event_browser"

  Scenario: Show tracking archive
    When I go to application "Chamilo\Core\Tracking" and do action "archiver"
