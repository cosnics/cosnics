Feature: chamilo.core.user.showapplication
  In order to use the application
  As an admin
  I should be able to load all pages

  Background:
    Given I am logged in

  Scenario: Show application
    When I go to application "Chamilo\Core\User"

  Scenario: Show user account
    When I go to application "Chamilo\Core\User" and do action "account"

  Scenario: Show users list
    When I go to application "Chamilo\Core\User" and do action "admin_user_browser"

  Scenario: Show user creator
    When I go to application "Chamilo\Core\User" and do action "creator"

  Scenario: Show users importer
    When I go to application "Chamilo\Core\User" and do action "importer"

  Scenario: Show users exporter
    When I go to application "Chamilo\Core\User" and do action "exporter"

  Scenario: Show user field browser
    When I go to application "Chamilo\Core\User" and do action "user_fields_builder"

  Scenario: Show user terms and conditions
    When I go to application "Chamilo\Core\User" and do action "terms_condition_editor"

  Scenario: Show user settings
    When I go to application "Chamilo\Core\User" and do action "user_settings"
