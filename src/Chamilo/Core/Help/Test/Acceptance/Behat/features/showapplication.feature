Feature: chamilo.core.help.showapplication
  In order to use the application
  As an admin
  I should be able to load all pages

  Background:
    Given I am logged in

  Scenario: Show application
    When I go to application "Chamilo\Core\Help"
