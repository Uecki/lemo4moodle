<?php
// This file is necessary to fulfill the privacy API of moodle.
// The block lemo4moodle does not store any personal data.
// Only already existing data from the moodle database is visualized.

namespace block_lemo4moodle\privacy;

class provider implements
    // This plugin does not store any personal user data.
    \core_privacy\local\metadata\null_provider {

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason() : string {
        return 'privacy:metadata';
    }
}
