# Manual for the moodle block "Lemo4Moodle"
___________________________________________

# Table of Contents

    1. GitHub-Link
    2. installation
        2.1 Installing Lemo4Moodle via Moodle (recommended)
        2.2 Installing Lemo4Moodle manually
    3. Implementation
        3.1 Permissions
        3.2 Including Lemo4Moodle in a course
    4. Further information
___________________________________________

# 1. Github-Link

    To get the newest version of the "Lemo4Moodle" block, simply visit
    https:// github.com/Uecki/lemo4moodle
    and download the repository, then follow the next steps.
___________________________________________

# 2. Installation

    2.1 Installing Lemo4Moodle via Moodle (recommended)

    To install Lemo4Moodle via Moodle, you first have to download the lemo4moodle.zip file
    found in the GitHub repository and make sure that you are logged in as an admin.

    !!! Important: there are two .zip files, lemo4moodle.zip and lemo4moodle_hwr.zip.
        You should only use the latter when installing this plugin for the HWR Berlin,
        because it doesn't follow all the moodle coding guidelines.

    Next, choose "Site administration" from the left sidebar, click on the tab
    "Plugins" and the click on "Install plugins".

    On that page, you simply have to select the lemo4Mooodle.zip file and Moodle
    does the rest for you.

    -----------------------------------

    2.2 Installing Lemo4Moodle manually

    To install Lemo4Moodle manually, either download the whole repository or just
    the .zip file. If you chose the .zip file, you have to unpack it first before
    moving on.

    Move the (unzipped) folder "lemo4moodle" into your moodle/blocks directory.
    E.g.: ../moodle/blocks/lemo4moodle.

    Next, log in to your moodle as an admin to install the new block.
    Upon login as an admin, you will be prompted to accept the
    installation of the new block. If you accept, the block will be installed.

    -----------------------------------

___________________________________________

# 3. Implementation

    3.1 Permissions

    Necessary permission for every role that should access the block: "View Block".

    If this permission is not give, please follow the instructions below.

    The trainer (or admin) role is required.

    1. Select a course and click on the gear symbol.

    2. Choose "More..." from the drop-down-menu.

    3. Select the "Users"-tab.

    4. Click on "Permissions".

    5. For the capability "View block", add each role that should be able to use
       the block as role with permission.

    -----------------------------------

    3.2 Including Lemo4Moodle in a course

    To include Lemo4Moodle in a course, simply make sure that your
    course is selected and click on the gear-symbol.

    Then click "Turn editing on", scroll down on the left side
    (selection of topics etc.) and select "Add a block".

    Finally, choose "Lemo4Moodle" from the list. The block is now added
    to your course.

___________________________________________

# 4. Further information

    For more information, please see the document "lemo4moodle_overview.pdf"
    from the GitHub repository. There, all the functionalities of Lemo4Moodle
    are explained and displayed by pictures.
