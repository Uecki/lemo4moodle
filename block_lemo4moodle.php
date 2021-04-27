<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file holds the class definition for the block, and is used both to manage it as a plugin and to render it onscreen.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Finn Ueckert, Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class that contains basic function needed for the block lemo4moodle.
 */
class block_lemo4moodle extends block_base {

    /**
     * Set the title when initialising the block.
     */
    public function init() {
        $this->title = get_string('lemo4moodle', 'block_lemo4moodle');
    }

    /**
     * Custom comparator used for sorting the array with usort() function.
     *
     * @return Returns the content of the block.
     */
    public function get_content() {

        // Import global vars -> (config.php).
        global $CFG;
        global $COURSE;
        global $USER;

        // If content not empty return it.
        if ($this->content !== null) {
            return $this->content;
        }

        // Create new standard class for plugin.
        $this->content = new stdClass;

        // Add text to plugin body.
        $this->content->text = '<a href= "'.$CFG->wwwroot.'/blocks/lemo4moodle/index.php?id='.$COURSE->id.'&user='.$USER->id.
            '" target="_blank"><img src="'.$CFG->wwwroot.
            '/blocks/lemo4moodle/pix/logo_180.png" alt="Logo Lemo4moodle" width="100px" height="100px"></a>';

        $this->content->footer = '';

        // Return content.
        return $this->content;

    }
}
