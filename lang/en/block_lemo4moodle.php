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
$string['lemo4moodle:addinstance'] = 'Add a new Lemo4Moodle block';
$string['lemo4moodle:myaddinstance'] = 'Add a new Lemo4Moodle block to the My Moodle page';
$string['content'] = 'CONTENT SETTINGS';
$string['privacy:metadata'] = 'The Lemo4Moodle block only displays existing data for actions inside the course.';

// General language strings (index.php).
$string['lang'] = 'en';
$string['download_dialog'] = '<b>Download only this graph or all graphs?</b><br><br>Data security notice:<br>When you open the downloaded file, external, non-Moodle libraries are loadedand used to visualize the downloaded data. The use is voluntary. The service cannot be provided without consent. By clicking on one of the download options, you accept that when you open the downloaded file, external services may access your IP address. These are the services JQuery, MaterializeCSS, Material Icons and Plotly.';
$string['modal_title'] = 'Merge files';
$string['modal_content'] = 'Now choose the files you want to merge the data from.<br>Should there be a gap between the time periods of the data saved in the files, then it will be displayed in the linechart tab of the merged file.<br>(Hold down the "ctrl"-button on your keyboard when selecting multiple files and click on each file you want to merge.)';
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
$string['selectStart'] = 'Select activity or resource:';
$string['selectAll'] = 'All';

// Moodle module names.
$string['assign'] = 'Assignment';
$string['assignment'] = 'Assignment';
$string['book'] = 'Book';
$string['chat'] = 'Chat';
$string['choice'] = 'Choice';
$string['data'] = 'Database';
$string['feedback'] = 'Feedback';
$string['folder'] = 'Folder';
$string['forum'] = 'Forum';
$string['glossary'] = 'Glossary';
$string['imscp'] = 'IMS content package';
$string['label'] = 'Label';
$string['lesson'] = 'Lesson';
$string['lti'] = 'External Tool';
$string['page'] = 'Page';
$string['quiz'] = 'Quiz';
$string['resource'] = 'File';
$string['scorm'] = 'SCORM package';
$string['survey'] = 'Survey';
$string['url'] = 'URL';
$string['wiki'] = 'Wiki';
$string['workshop'] = 'Workshop';


// Barchart.
$string['barchart_title'] = 'Accesses and users per activity or resource';
$string['barchart_xlabel'] = 'Name';
$string['barchart_ylabel'] = 'Accesses';
$string['barchart_users'] = 'Users';
$string['barchart_module'] = 'Module';

// Linechart.
$string['linechart_colDate'] = 'Date';
$string['linechart_colAccess'] = 'Accesses';
$string['linechart_colOwnAccess'] = 'Own accesses';
$string['linechart_colUser'] = 'Users';
$string['linechart_colMissingData'] = 'Missing Data';
$string['linechart_title'] = 'Accesses and users per day';

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

// Treemap.
$string['treemap_title'] = 'Treemap that shows the number of clicks per file. Right click to get one level higher.';
$string['treemap_clickCount'] = ' Number of clicks: ';
$string['treemap_global'] = 'Global';
$string['treemap_files'] = 'Files';

// View.
$string['view_checkSelection'] = 'Please check your selection (start > end).';
$string['view_dialogThis'] = 'This graph';
$string['view_dialogAll'] = 'All graphs';
$string['view_file'] = 'File ';
$string['view_timespan'] = 'Timespan: ';
$string['view_noTimespan'] = 'Timespan could not be determined.';
$string['view_modalError'] = 'Please choose at least two files to merge.';
