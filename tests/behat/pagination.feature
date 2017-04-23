@ou @ou_vle @mod @mod_openstudio @mod_openstudio_test_content @_file_upload @javascript
Feature: Create and edit contents
    When using Open Studio with other users
    As a teacher
    I need to navigate to content pages

    Background: Setup course and studio
        Given the following "users" exist:
            | username | firstname | lastname | email |
            | teacher1 | Teacher | 1 | teacher1@asd.com |
            | student1 | Student | 1 | student1@asd.com |
            | student2 | Student | 2 | student2@asd.com |
            | student3 | Student | 3 | student3@asd.com |
            | student4 | Student | 4 | student4@asd.com |
        And the following "courses" exist:
            | fullname | shortname | category |
            | Course 1 | C1 | 0 |
        And the following "course enrolments" exist:
            | user | course | role |
            | teacher1 | C1 | editingteacher |
            | student1 | C1 | student |
            | student2 | C1 | student |
            | student3 | C1 | student |
            | student4 | C1 | student |
        And the following "groups" exist:
            | name   | course | idnumber |
            | group1 | C1     | G1       |
            | group2 | C1     | G2       |
            | group3 | C1     | G3       |
        And the following "groupings" exist:
            | name      | course | idnumber |
            | grouping1 | C1     | GI1      |
        And the following "grouping groups" exist:
            | grouping | group |
            | GI1      | G1    |
            | GI1      | G2    |
            | GI1      | G3    |
        And the following "group members" exist:
            | user     | group  |
            | teacher1 | G1 |
            | student1 | G1 |
            | student2 | G1 |
            | teacher1 | G2 |
            | student2 | G2 |
            | student3 | G2 |
            | student3 | G3 |
        And I log in as "teacher1"
        And I am on site homepage
        And I follow "Course 1"
        And I turn editing mode on
        And I add a "Open Studio" to section "1" and I fill the form with:
            | Name                         | Test Open Studio name 1      |
            | Description                  | Test Open Studio description |
            | Your word for 'My Module'    | Module 1                     |
            | Group mode                   | Separate groups              |
            | Grouping                     | grouping1                    |
            | Enable pinboard              | 99                           |
            | Abuse reports are emailed to | teacher1@asd.com             |
            | ID number                    | OS1                          |
        And Open Studio test instance is configured for "Test Open Studio name 1"
        And all users have accepted the plagarism statement for "OS1" openstudio

    Scenario: Test Pagination without contents
        When I follow "Test Open Studio name 1"
        Then I should not see "TestSlot 12"
        Then I should not see "Next"

    Scenario: Test Pagination with contents
        When I log out
        And I log in as "admin"
        And I navigate to "Open Studio" node in "Site administration > Plugins >Activity modules > Open Studio"
        And I set the field "Stream pagination size" to "12"
        And I press "Save changes"
        And I log out
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test Open Studio name 1"
        And the following open studio "contents" exist:
            | openstudio | user     | name       | description             | visibility |
            | OS1        | student1 | TestSlot A | Test slot 1 description | module     |
        And I wait "1" seconds
        And the following open studio "contents" exist:
            | openstudio | user     | name        | description              | visibility |
            | OS1        | student1 | TestSlot B  | Test slot 2 description  | module     |
            | OS1        | student1 | TestSlot C  | Test slot 3 description  | module     |
            | OS1        | student1 | TestSlot D  | Test slot 4 description  | module     |
            | OS1        | student1 | TestSlot E  | Test slot 5 description  | module     |
            | OS1        | student1 | TestSlot F  | Test slot 6 description  | module     |
            | OS1        | student1 | TestSlot G  | Test slot 7 description  | module     |
            | OS1        | student1 | TestSlot H  | Test slot 8 description  | module     |
            | OS1        | student1 | TestSlot I  | Test slot 9 description  | module     |
            | OS1        | student1 | TestSlot K | Test slot 10 description | module     |
            | OS1        | student1 | TestSlot L | Test slot 11 description | module     |
            | OS1        | student1 | TestSlot M | Test slot 12 description | module     |
        And I wait "1" seconds
        And the following open studio "contents" exist:
            | openstudio | user     | name        | description              | visibility |
            | OS1        | student1 | TestSlot N | Test slot 13 description | module     |

        When I reload the page
        And I follow "Shared content > My Module" in the openstudio navigation
        Then I should see "TestSlot N"
        And I should see "2"
        When I follow "2"
        Then I should see "TestSlot A"
        And I should not see "TestSlot N"
        And I should see "1"
        When I follow "1"
        Then I should see "TestSlot N"
