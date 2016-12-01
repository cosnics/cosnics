Feature: chamilo.core.groups.showapplication
  In order to use the application
  As an admin
  I should be able to load all pages

  Background:
    Given I am logged in

  Scenario: Show application
    When I go to application "Chamilo\Core\Group"

  Scenario: Show group list
    When I go to application "Chamilo\Core\Group" and do action "browser"

  Scenario: Show group exporter
    When I go to application "Chamilo\Core\Group" and do action "exporter"

  Scenario: Show group creator
    When I go to application "Chamilo\Core\Group" and do action "creator"

  Scenario: Show group importer
    When I go to application "Chamilo\Core\Group" and do action "importer"

  Scenario: Show group user importer
    When I go to application "Chamilo\Core\Group" and do action "group_user_importer"
