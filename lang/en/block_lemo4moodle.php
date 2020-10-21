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
 * This file contains all the english language strings used by this block.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Finn Ueckert, Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Lemo4Moodle';
$string['lemo4moodle'] = 'Lemo4Moodle';
$string['lemo4moodle:addinstance'] = 'Add a new Lemo4moodle block';
$string['lemo4moodle:myaddinstance'] = 'Add a new Lemo4moodle block to the My Moodle page';
$string['content'] = 'CONTENT SETTINGS';
$string['privacy:metadata'] = 'The lemo4moodle block only displays existing data for actions inside the course.';

// General language strings (index.php).
$string['lang'] = 'en';
$string['download_dialog'] = '<b>Download only this graph or all graphs?</b><br><br>Data security notice:<br>When you open the downloaded file, external, non-Moodle libraries are loadedand used to visualize the downloaded data. The use is voluntary. The service cannot be provided without consent. By clicking on one of the download options, you accept that when you open the downloaded file, external services may access your IP address. These are the services JQuery, MaterializeCSS, Material Icons and Plotly.';
$string['modal_title'] = 'Merge files';
$string['modal_content'] = 'Now choose the files you want to merge the data from.<br>At the moment, <u>only</u> the linechart data will be merged and displayed in the downloaded file.<br>(Hold down the "ctrl"-button on your keyboard when selecting multiple files and click on each file you want to merge.)';
$string['logdata'] = 'Logdata since: ';
$string['backup'] = 'Backup your data: ';
$string['filter'] = 'Filter';
$string['course_activity'] = 'Course activity (Moodle report)';
$string['update'] = 'Update';
$string['reset'] = 'Reset';
$string['html_download'] = 'HTML Download';
$string['dialogTitle'] = 'Choice';
$string['filterStart'] = 'Start';
$string['filterEnd'] = 'End';
$string['selectStart'] = 'Filter modules:';
$string['selectAll'] = 'All';

// Moodle module names.
$string['mod_assign'] = 'Assignment';
$string['mod_assignment'] = 'Assignment';
$string['mod_book'] = 'Book';
$string['mod_chat'] = 'Chat';
$string['mod_choice'] = 'Choice';
$string['mod_data'] = 'Database';
$string['mod_feedback'] = 'Feedback';
$string['mod_folder'] = 'Folder';
$string['mod_forum'] = 'Forum';
$string['mod_glossary'] = 'Glossary';
$string['mod_imscp'] = 'IMS content package';
$string['mod_label'] = 'Label';
$string['mod_lesson'] = 'Lesson';
$string['mod_lti'] = 'External Tool';
$string['mod_page'] = 'Page';
$string['mod_quiz'] = 'Quiz';
$string['mod_resource'] = 'File';
$string['mod_scorm'] = 'SCORM package';
$string['mod_survey'] = 'Survey';
$string['mod_url'] = 'URL';
$string['mod_wiki'] = 'Wiki';
$string['mod_workshop'] = 'Workshop';


// Barchart.
$string['barchart_title'] = 'Accesses and users per file';
$string['barchart_xlabel'] = 'Filename';
$string['barchart_ylabel'] = 'Accesses';
$string['barchart_users'] = 'Users';
$string['barchart_module'] = 'Module';

// Linechart.
$string['linechart_colDate'] = 'Date';
$string['linechart_colAccess'] = 'Accesses';
$string['linechart_colOwnAccess'] = 'Own accesses';
$string['linechart_colUser'] = 'Users';
$string['linechart_title'] = 'Accesses and users per day';
$string['linechart_checkSelection'] = 'Please check your selection (start > end).';

// Heatmap.
$string['heatmap_title'] = 'Actions per day per timespan';
$string['heatmap_all'] = 'ALL';
$string['heatmap_own'] = 'OWN';
$string['heatmap_overall'] = 'Overall';
$string['heatmap_average'] = 'Average';
$string['heatmap_monday'] = 'Monday';
$string['heatmap_tuesday'] = 'Tuesday';
$string['heatmap_wednesday'] = 'Wednesday';
$string['heatmap_thursday'] = 'Thursday';
$string['heatmap_friday'] = 'Friday';
$string['heatmap_saturday'] = 'Saturday';
$string['heatmap_sunday'] = 'Sunday';
$string['heatmap_checkSelection'] = 'Please check your selection (start > end).';

// Treemap.
$string['treemap_title'] = 'Treemap that shows the number of clicks per file. Right click to get one level higher.';
$string['treemap_clickCount'] = ' Number of clicks: ';
$string['treemap_global'] = 'Global';
$string['treemap_files'] = 'Files';

// View.
$string['view_dialogThis'] = 'This graph';
$string['view_dialogAll'] = 'All graphs';
$string['view_file'] = 'File ';
$string['view_timespan'] = 'Timespan: ';
$string['view_noTimespan'] = 'Timespan could not be determined.';
$string['view_modalError'] = 'Please choose at least two files to merge.';
