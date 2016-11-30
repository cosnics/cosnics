Feature: chamilo.core.admin.showapplication
  In order to use the application
  As an admin
  I should be able to load all pages

  Background:
    Given I am logged in

  Scenario: Show application
    When I go to application "Chamilo\Core\Admin"

  Scenario: Show administration setting
    When I go to application "Chamilo\Core\Admin" and do action "configurer"

  Scenario: Show administration importer
    When I go to application "Chamilo\Core\Admin" and do action "importer"

  Scenario: Show administration system announcement
    When I go to application "Chamilo\Core\Admin" and do action "announcer"

  Scenario: Show administration translations
    When I go to application "Chamilo\Core\Admin" and do action "language"

  Scenario: Show administration diagnose
    When I go to application "Chamilo\Core\Admin" and do action "diagnoser"

  Scenario: Show administration log viewer
    When I go to application "Chamilo\Core\Admin" and do action "log_viewer"
