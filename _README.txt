!!! Not up to date !!!


# Manual for the Moodle Block "Lemo4Moodle"
___________________________________________

# Table of Contents
	
	1. GitHub-Link
	2. config.php
	3. Implementation
		3.1 Installing Lemo4Moodle
		3.2 Necessary preparations in moodle
		3.3 Including Lemo4Moodle in a course
___________________________________________

# 1. Github-Link

	To get the newest version of the "Lemo4Moodle" block, simply visit
	https://github.com/Uecki/lemo4moodle
	and download the repository, then follow the next steps.
___________________________________________

# 2. config.php

	The file config.php is located directly inside the lemo4moodle-
	directory.
	../moodle/blocks/lemo4moodle/config.php
	Here, edit the database access data (4 constants) to suit your
	database.
	Also change the constant moodle_url to the url, with which you access
	your moodle.
	The path to the lemo4moodle directory should be generated 
	automatically. If this is not the case, simply exchange
	$realPath with the correct path in your directory.
	
___________________________________________

# 3. Implementation
	
	3.1 Installing Lemo4Moodle

	Move the folder "lemo4moodle" into your moodle/blocks directory.
	E.g.: ../moodle/blocks/lemo4moodle
	
	Next, log in to your moodle as an admin to install the new block.
	Upon login as an admin, you will be prompted to accept the 
	installation of the new block.

	-----------------------------------

	3.2 Necessary preparations in moodle

	English:

		The trainer (or admin) role is are required.	

		1. Select a course and click on the gear symbol.

		2. Choose "More..." from the drop-down-menu.

		3. Select the "Users"-tab.

		4. Click on "Permissions". 
		
		5. For the capabilities 
		"View block" and "Manage blocks on a page", add 		
		"Authenticated user" and "Student" as Role with permission.

	Deutsch:
		
		Mindestens Trainer/in Rechte sind hierfür nötig

		1. Bei der Kursansicht auf das Zahnradsymbol klicken.

		2. Dort  im Drop-Down Menü "Mehr..." wählen.

		3. Den Reiter "Nutzer/innen" auswählen.

		4. Auf "Rechte ändern" klicken.

		5. Die Optionen "Block sehen" und "Blöcke auf dieser Seite 	
		verwalten" für authentifizierte Nutzer und Teilnehmer 		
		freigeben.


	-----------------------------------

	3.3 Including Lemo4Moodle in a course

	To include Lemo4Moodle in a course, simply make sure that your 
	course is selected and click on the gear-symbol. 
	Then click "Turn editing on", scroll down on the left side
 	(selection of topics etc.) and select "Add a block".
	Finally, choose "Lemo4Moodle" from the list. The block is now added
	to your course.