Feature: application.weblcms.course_type.create_course_type
  In order to manage courses
  As a admin
  I need to be able to create a course type

Scenario: Create a course type with default values
  Given I am on "/index.php?application=application\weblcms&go=course_type_manager&course_type_action=create"
  And I am logged in as "admin"
  When I fill in "title" with "General Courses"
  And I fill in "description" with "General courses with default settings"
  And I check "active"
  And I press "Save"
  Then I should see "General Courses"
  And I should see a success box