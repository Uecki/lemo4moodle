<?php
 
class block_lemo4moodle_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
 
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('content', 'block_lemo4moodle'));
 
        // A sample string variable with a default value.
        $mform->addElement('text', 'config_text', get_string('text', 'block_lemo4moodle'));
        $mform->setDefault('config_text', 'default_value');
        $mform->setType('config_text', PARAM_RAW);      

		// A sample string variable with a default value.
		$mform->addElement('text', 'config_title', get_string('blocktitle', 'block_lemo4moodle'));
		$mform->setDefault('config_title', 'title_default');
		$mform->setType('config_title', PARAM_TEXT);
 
    }
}