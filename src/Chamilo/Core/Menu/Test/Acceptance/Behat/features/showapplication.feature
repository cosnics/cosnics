Feature: chamilo.core.menu.showapplication
  In order to use the application
  As an admin
  I should be able to load all pages

  Background:
    Given I am logged in

  Scenario: Show application
    When I go to application "Chamilo\Core\Menu"

  Scenario: Show menu setting
    When I go to application "Chamilo\Core\Menu" and do action "browser"

  Scenario: Show menu rights setting
    When I go to application "Chamilo\Core\Menu" and do action "rights"

  Scenario: Show menu editor
    When I go to application "core\menu" and do action "editor"
