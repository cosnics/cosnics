Feature: chamilo.core.metadata.showapplication
  In order to use the application
  As an admin
  I should be able to load all pages

  Background:
    Given I am logged in

  Scenario: Show application
    When I go to application "Chamilo\Core\Metadata"

  Scenario: Show metadata browser
    When I go to application "Chamilo\Core\Metadata" and do action "schema"

  Scenario: Show metadata browser
    When I go to application "Chamilo\Core\Metadata" and do action "element"

  Scenario: Show metadata browser
    When I go to application "Chamilo\Core\Metadata" and do action "attribute"

  Scenario: Show metadata browser
    When I go to application "Chamilo\Core\Metadata" and do action "controlled_vocabulary"

  Scenario: Show metadata browser
    When I go to application "Chamilo\Core\Metadata" and do action "metadata_exporter"

  Scenario: Show metadata browser
    When I go to application "Chamilo\Core\Metadata" and do action "metadata_importer"
