@office365available
Feature: chamilo.core.repository.implementation.office365video.view_and_import_videos
  In order to use the application
  I should be able to load all pages

  Background:
    Given I am logged in
    And I go to application "Chamilo\Core\Repository" and do action "external_instance_manager"
      
  Scenario: Select office365 external repostory
    When I follow "Voeg externe connectie toe"
    And I follow "Office 365 Video"
    Then I should see "General" 
    And I should see "Settings"

  Scenario: Create office365 repository
    When I follow "Voeg externe connectie toe"
    And I follow "Office 365 Video"
    And I fill in configured client id, secret, and sharepoint URL
    And I fill in "title" with "Office 365 Videos"
    And I fill in "description" with "Office 365 Videos"
    And I check "enabled"
    And press "Maak"
    Then I should see a success box
    And I should see "Office 365 Videos"

  # If logging on to office365 site fails, check first whether you can log on manually.
  Scenario: Log on to office365
    When I follow "Office 365 Videos"
    And I submit configured user id and password
    Then I should see "Uitloggen"
  
  # This test expects that you have already created a channel called "Channel1" and a video named "Video1" via the office365/Video site.     
  Scenario: List available office365 video's
    When I follow "Office 365 Videos"
    And I follow "Channel1"
    Then I should see "Video1"

  Scenario: View video details
    When I follow "Office 365 Videos"
    And I follow "Channel1"
    And I click on thumbnail "Video1"
    Then I should see "Titel"
    And I should see "Video1"
    And I should see "Beschrijving"
    And I should see "Opgeladen op"
    And I should see "Eigenaar"
    And I should see "Status"
    And I should see "Beschikbaar"
        
  Scenario: Import as content object
    When I follow "Office 365 Videos"
    And I follow "Channel1"
    And I click on thumbnail "Video1"
    And I follow "Importeer"
    Then I should see "Mijn repository"
    And I should see "Bekijk Video1"
    And I should see "Office 365 Video"
    And I should see "Verwijder"
    And I should see "Bewerk"
  
  Scenario: Jump to external video from imported content object
    Given I go to application "Chamilo\Core\Repository"
    When I follow "Video1"
    And I follow "Toon" in the row containing "Office 365 Videos"
    Then I should see "Titel"
    And I should see "Video1"
    And I should see "Beschrijving"
    And I should see "Opgeladen op"
    And I should see "Eigenaar"
    And I should see "Status"
    And I should see "Beschikbaar"

  Scenario: Remove imported content object
    Given I go to application "Chamilo\Core\Repository"
    And I follow "Verwijder" in the row containing "Video1"
    And I follow "Prullenbak"
    And I follow "Verwijder" in the row containing "Video1"
    Then I should see a success box
  	  	       
  Scenario: Log out of office365 
    When I follow "Office 365 Videos"
    And I follow "Uitloggen"
    Then I should see "Inloggen"

  Scenario: Remove office365 repository
    When I follow "Verwijder" in the row containing "Office 365 Videos"
    Then I should see a success box
