@office365available
Feature: chamilo.core.repository.implementation.office365.view_and_import_documents
  In order to use the application
  I should be able to load all pages

  Background:
    Given I am logged in
    And I go to application "Chamilo\Core\Repository" and do action "external_instance_manager"
  
  Scenario: Select office365 external repostory
    When I follow "Voeg externe connectie toe"
    And I follow "Office 365"
    Then I should see "General" 
    And I should see "Settings"

  Scenario: Create office365 repository
    When I follow "Voeg externe connectie toe"
    And I follow "Office 365"
    And I fill in configured client id and secret
    And I fill in "title" with "Office 365 Documents"
    And I fill in "description" with "Office 365 Documents"
    And I check "enabled"
    And press "Maak"
    Then I should see a success box
    And I should see "Office 365 Documents"

  # If logging on to office365 site fails, check first whether you can log on manually.
  Scenario: Log on to office365
    When I follow "Office 365 Documents"
    And I fill in configured user id and password
    And press "Sign in"
    Then I should see "Uitloggen"
  
  # This test expects that you have already created a folder called "Folder1" and document named "Book.xlsx" via the office365/OneDrive site. 
  Scenario: List available office365 documents
    When I follow "Office 365 Documents"
    Then I should see "Book.xlsx"
    And I should see "Folder1"
  
  # This test expects that you have already created a folder called "Folder1" and document named "Document.xlsx" in it, via the office365/OneDrive site.   
  Scenario: List available office365 documents in Folder1
    When I follow "Office 365 Documents"
    And I follow "Folder1"
    Then I should see "Document.docx"
  
  Scenario: Move up and down between folders
    When I follow "Office 365 Documents"
    And I follow "Folder1"
    And I follow "root"
    Then I should see "Book.xlsx"
  
  Scenario: View document details
    When I follow "Office 365 Documents"
    And I follow "Book.xlsx"
    Then I should see "Titel"
    And I should see "Book.xlsx"
    And I should see "Opgeladen op"
    And I should see "Gewijzigd op"
    And I should see "Eigenaar"
    And I should see "Laatst gewijzigd"
    And I should see "URL"

  Scenario: Import as File content object
    When I follow "Office 365 Documents"
    And I follow "Book.xlsx"
    And I follow "Importeer als bestand"
    Then I should see "Mijn repository"
    And I should see "Bekijk Book.xlsx"
    And I should see "Bestand"
    And I should see "Verwijder"
    And I should see "Bewerk"
  
  Scenario: Jump to external document from imported file content object
    Given I go to application "Chamilo\Core\Repository"
    When I follow "Book.xlsx"
    And I follow "Toon" in the row containing "Office 365 Documents"
    Then I should see "Titel"
    And I should see "Book.xlsx"
    And I should see "Opgeladen op"
    And I should see "Gewijzigd op"
    And I should see "Eigenaar"
    And I should see "Laatst gewijzigd"
    And I should see "URL"

  Scenario: Remove imported file content object
    Given I go to application "Chamilo\Core\Repository"
    And I follow "Verwijder" in the row containing "Book.xlsx"
    And I follow "Prullenbak"
    And I follow "Verwijder" in the row containing "Book.xlsx"
    Then I should see a success box
  
  Scenario: Import as Link content object
    When I follow "Office 365 Documents"
    And I follow "Book.xlsx"
    And I follow "Importeer als link"
    Then I should see "Mijn repository"
    And I should see "Bekijk Book.xlsx"
    And I should see "Link"
    And I should see "Verwijder"
    And I should see "Bewerk"
  
  Scenario: Jump to external document from imported link content object
    Given I go to application "Chamilo\Core\Repository"
    When I follow "Book.xlsx"
    And I follow "Toon" in the row containing "Office 365 Documents"
    Then I should see "Titel"
    And I should see "Book.xlsx"
    And I should see "Opgeladen op"
    And I should see "Gewijzigd op"
    And I should see "Eigenaar"
    And I should see "Laatst gewijzigd"
    And I should see "URL"

  Scenario: Remove imported file content object
    Given I go to application "Chamilo\Core\Repository"
    And I follow "Verwijder" in the row containing "Book.xlsx"
    And I follow "Prullenbak"
    And I follow "Verwijder" in the row containing "Book.xlsx"
    Then I should see a success box

  Scenario: Log out of office365 
    When I follow "Office 365 Documents"
    And I follow "Uitloggen"
    Then I should see "Inloggen"

  Scenario: Remove office365 repository
    When I follow "Verwijder" in the row containing "Office 365 Documents"
    Then I should see a success box
