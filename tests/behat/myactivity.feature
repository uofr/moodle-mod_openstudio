@ou @ou_vle @mod @mod_openstudio @mod_openstudio_manage_folders @javascript
Feature: My Activity view in Open Studio
  When using Open Studio with other users
  As a teacher
  I need to create a content and upload a file

  Background: Setup course and studio
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course    | role           |
      | teacher1 | C1        | editingteacher |
    And the following "groups" exist:
      | name     | course    | idnumber |
      | group1   | C1        | G1       |
    And the following "groupings" exist:
      | name     | course    | idnumber |
      | grouping1| C1        | GI1      |
    And the following "grouping groups" exist:
      | grouping | group |
      | GI1      | G1    |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "OpenStudio 2 (pilot only)" to section "1" and I fill the form with:
      | Name                         | Test Open Studio name 1      |
      | Description                  | Test Open Studio description |
      | Group mode                   | Visible groups               |
      | Grouping                     | grouping1                    |
      | Enable pinboard              | 99                           |
      | Enable Folders               | 1                            |
      | Abuse reports are emailed to | teacher1@asd.com             |
      | ID number                    | OS1                          |
    And all users have accepted the plagarism statement for "OS1" openstudio
    And I follow "Test Open Studio name 1"
    And I follow "Administration > Manage levels" in the openstudio navigation
    And I press "Add another Block"
    And I set the field "Block Name" to "Block 1"
    And I press "Save Changes"
    And I follow "Block 1"
    And I press "Add another Activity"
    And I set the field "Activity Name" to "Activity 1"
    And I press "Save Changes"
    And I press "Add another Activity"
    And I set the field "Activity Name" to "Activity 2"
    And I press "Save Changes"
    And I press "Add another Activity"
    And I set the field "Activity Name" to "Activity 3"
    And I press "Save Changes"
    And I follow "Activity 1"
    And I press "Add another Content"
    And I set the field "Content Name" to "Content 1.1 Required"
    And I set the field "Required (TMA)" to "1"
    And I press "Add another Content"
    And I follow "Manage levels"
    And I follow "Block 1"
    And I follow "Activity 3"
    And I press "Add another Content"
    And I set the field "Content Name" to "Content 3.1"
    And I press "Add another Content"
    
  Scenario: Show My Activity Board View
    When I follow "Test Open Studio name 1"
    And I follow "My Content > My Activities" in the openstudio navigation
    Then I should see "Activity 1"
    And I should not see "Activity 2"
    And I should see "Activity 3"
  
  Scenario: Upload a new content without file upload
    When I follow "Test Open Studio name 1"
    And I follow "My Content > My Activities" in the openstudio navigation
    And I follow "Content 1.1 Required"
    And I set the following fields to these values:
      | Who can view this content | My module                   |
      | Title                     | Test My Activities View 1   |
      | Description               | My Activities Description 1 |
    And I press "Save"
    And I follow "My Content > My Activities" in the openstudio navigation
    And I should see "Test My Activities View 1"
    And I should see "Content 1.1 Required"
    Then the "src" attribute of "img.openstudio-grid-item-thumbnail" "css_element" should contain "online_rgb_32px"

  Scenario: Upload a new content with file upload
    When I follow "Test Open Studio name 1"
    And I follow "My Content > My Activities" in the openstudio navigation
    And I follow "Content 1.1 Required"
    And I press "Add file"
    And I set the following fields to these values:
      | Who can view this content | My module                                  |
      | Title                     | Test My Activities View 2                  |
      | Description               | My Activities Description 2                |
      | Upload content            | mod/openstudio/tests/importfiles/test1.jpg |
    And I press "Save"
    And I follow "My Content > My Activities" in the openstudio navigation
    And I should see "Test My Activities View 2"
    And I should see "Content 1.1 Required"
    Then the "src" attribute of "img.openstudio-grid-item-thumbnail" "css_element" should contain "test1.jpg"

  Scenario: Upload a new content with Add web/embed link
    When I follow "Test Open Studio name 1"
    And I follow "My Content > My Activities" in the openstudio navigation
    And I follow "Content 1.1 Required"
    And I press "Add web/embed link"
    And I set the following fields to these values:
      | Who can view this content | My module                                    |
      | Title                     | Test My Activities View 3                    |
      | Description               | Test My Activities View 3 Add web/embed link |
      | Web link                  | https://www.youtube.com/watch?v=ktAnpf_nu5c  |
    And I press "Save"
    And I follow "My Content > My Activities" in the openstudio navigation
    And I should see "Test My Activities View 3"
    And I should see "Content 1.1 Required"
    Then the "src" attribute of "img.openstudio-grid-item-thumbnail" "css_element" should contain "online_rgb_32px"

  Scenario:  Edit the setting of the new content uploaded
    When I follow "Test Open Studio name 1"
    And I follow "My Content > My Activities" in the openstudio navigation
    And I follow "Content 1.1 Required"
    And I press "Add file"
    And I set the following fields to these values:
      | Who can view this content | My module                                  |
      | Title                     | Test My Activities View 4                  |
      | Description               | My Activities Description 4                |
      | Upload content            | mod/openstudio/tests/importfiles/test2.jpg |
    And I press "Save"
    And I follow "My Content > My Activities" in the openstudio navigation
    And I click on "div.openstudio-grid-item-content" "css_element"
    And I go to content edit view
    And I set the following fields to these values:
      | Who can view this content | My module                                  |
      | Title                     | Test My Activities View 5                  |
      | Description               | My Activities Description 5                |
    And I press "Save"
    And I follow "My Content > My Activities" in the openstudio navigation
    And I should see "Test My Activities View 5"
    And I should not see "Test My Activities View 4"
    And I should see "Content 1.1 Required"
    Then the "src" attribute of "img.openstudio-grid-item-thumbnail" "css_element" should contain "test2.jpg"

  Scenario: Don't show upload buttons when no additional content is allowed.
    When I set the following fields to these values:
      | Content Name | Folder content 3.2 |
      | Is folder?   | 1                  |
    And I press "Save Changes"
    And I follow "Folder content 3.2"
    And I set the following fields to these values:
      | Number of additional contents allowed | 0 |
    And I press "Save Changes"
    And I follow "Test Open Studio name 1"
    And I follow "My Content > My Activities" in the openstudio navigation
    And I follow "Folder content 3.2"
    # Check add section is hidden.
    Then I should not see "Add new content"
    And I should not see "Upload content to folder"
    And I should not see "Select existing post to add to folder"
