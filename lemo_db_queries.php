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
 * This file contains the queries to the moodle database.
 *
 * The resutls of the queries are also already made
 * usable for the charts. This file is included in index.php.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Finn Ueckert, Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$DB->set_debug(true);

// SQL Query -> ActivityChart (date, hits, user counter).
$querylinechart = "SELECT FROM_UNIXTIME (timecreated, '%d-%m-%Y') AS 'date', COUNT(action) AS 'allHits',
                                COUNT(DISTINCT userid) AS 'users', COUNT(CASE WHEN " .  $DB->sql_compare_text('userid') . " = " . $DB->sql_compare_text(':userid') . "
                                THEN $userid END) AS 'ownhits'
                           FROM {logstore_standard_log}
                          WHERE (" .  $DB->sql_compare_text('action') . " = " . $DB->sql_compare_text(':action') . "
                                AND " .  $DB->sql_compare_text('courseid') . " = " . $DB->sql_compare_text(':courseid') . ")
                       GROUP BY FROM_UNIXTIME (timecreated, '%y-%m-%d')
                       ORDER BY 'date'";

//Query function parameters.
$params = ['userid' => $userid, 'action' => 'viewed', 'courseid' => $courseid];

$linechart = $DB->get_records_sql($querylinechart, $params);

unset($params);

// Transform result of the query from Object to an array of Objects.
$linechartdata = array();
foreach ($linechart as $l) {
    $linechartdata[] = $l;
}


// Get the first recorded date of the datasets (used to indicate the first date of data-timespan in index.php).
$splitdate = explode("-", $linechartdata[0]->date);
$firstdateindex = $splitdate[0] . '.' . $splitdate[1] . '.' . $splitdate[2];


// SQL Query for bar chart data.

$querybarchart = "SELECT LOGS1.id, FROM_UNIXTIME (LOGS1.timecreated, '%d-%m-%Y') AS 'date', LOGS1.component, LOGS2.timecreated, LOGS2.other
                    FROM {logstore_standard_log} LOGS1
              INNER JOIN (SELECT contextid, timecreated, other
                            FROM {logstore_standard_log}
                           WHERE " .  $DB->sql_compare_text('action') . " = " . $DB->sql_compare_text(':action') . ") LOGS2
                      ON LOGS1.contextid = LOGS2.contextid
                   WHERE " . $DB->sql_like('LOGS1.component', ':component') . "
                            AND " .  $DB->sql_compare_text('LOGS1.action') . " = " . $DB->sql_compare_text(':action2') . "
                            AND " .  $DB->sql_compare_text('LOGS1.courseid') . " = " . $DB->sql_compare_text(':courseid') . "
                            AND LOGS1.objecttable IS NOT NULL
                ORDER BY LOGS2.timecreated ASC";

//Query function parameters.
$params = ['action' => 'created', 'component' => 'mod%', 'action2' => 'viewed', 'courseid' => $courseid];

// Perform SQL-Query.
$barchart = $DB->get_records_sql($querybarchart, $params);

unset($params);

// Get module information of the current course to later complement the query.
GLOBAL $COURSE;
// Use moodle function get_fast_modinfo().
$modinfo = get_fast_modinfo($COURSE);
$modules = array();
// Add name and modulename of each object in the course to an associative array with the time an object was added to the course as key.
foreach ($modinfo->get_cms() as $cminfo) {
    $modules[$cminfo->added] = array('name' => $cminfo->name, 'module' => 'mod_' . $cminfo->modname);
}

// Sort array by key.
ksort($modules);

// Transform associative array to array.
$modulesarray = array();
foreach($modules as $m) {
    $modulesarray[] = $m;
}


// Transform result of the query from Object to an array of Objects.
$barchartdata = array();
foreach ($barchart as $b) {
    $barchartdata[] = $b;
}

$prevtime = 0; // Contains timestamp of the previous loop.
$indexmodule = -1; // Index for modulesarray.

// Assign the name, stored in modulesarray, to the barchartdata array.
for($i = 0; $i < sizeof($barchartdata); $i++) {
    // If the time of creation of the object is different, then it is the next unique
    // object in the array and can be assigned the next name taken from $modulesarray.
    if($barchartdata[$i]->timecreated != $prevtime) {
        $indexmodule++;
        // Because $modulesarray also stores the objects contained in folders, whose views
        // are not contained in the logstore_standard_log, those have to be filtered out.
        // To achieve this, the modulenames of both arrays sorted by time of creation are
        // compared and diversions in $modulesarray are skipped.
        if($barchartdata[$i]->component != $modulesarray[$indexmodule]['module']) {
            $indexmodule++;
        }
        $barchartdata[$i]->name = $modulesarray[$indexmodule]['name'];
    }
    else {
        // Assigns previous name, because both elements have the same time of creation and are
        // therefore assumed to be the same.
        $barchartdata[$i]->name = $barchartdata[$i - 1]->name;
    }
    $prevtime = $barchartdata[$i]->timecreated;

    // Replace the component (module) name with the string from the language file.
    $barchartdata[$i]->component = get_string($barchartdata[$i]->component, 'block_lemo4moodle');

}


// Query for heatmap. Only minor changes to activity chart query.

$queryheatmap = "SELECT  id, timecreated, FROM_UNIXTIME(timecreated, '%W') AS 'weekday', FROM_UNIXTIME(timecreated, '%k') AS 'hour',
                            COUNT(action) AS 'allHits',  COUNT(CASE WHEN " .  $DB->sql_compare_text('userid') . " = " . $DB->sql_compare_text(':userid') . "
                            THEN $userid END) AS 'ownhits'
                       FROM {logstore_standard_log}
                      WHERE (" .  $DB->sql_compare_text('action') . " = " . $DB->sql_compare_text(':action') . "
                            AND " .  $DB->sql_compare_text('courseid') . " = " . $DB->sql_compare_text(':courseid') . ")
                     GROUP BY timecreated"; // Group by hour.

//Query function parameters.
$params = ['userid' => $userid, 'action' => 'viewed', 'courseid' => $courseid];

$heatmap = $DB->get_records_sql($queryheatmap, $params);

unset($params);

// Transform result of the query from Object to array of Objects.
$heatmaptdata = array();
foreach ($heatmap as $h) {
    $h->date = date("d-m-Y", $h->timecreated);
    $heatmapdata[] = $h;
}

$DB->set_debug(false);


// Create dataarray.
// Data as JSON [activityData[date, overallHits, ownhits, users], barchartdata[name, hits, users],
// treemapdata[name, title, hits, color(as int)]].
$dataarray = array();
$dataarray[] = $linechartdata;
$dataarray[] = $barchartdata;
$dataarray[] = $heatmapdata;
//$dataarray[] = $treemapdataarray;

// Encode dataarray as JSON !JSON_NUMERIC_CHECK.
$alldata = str_replace("'", "\'", json_encode($dataarray));
$alldatahtml = str_replace("'", "&#39;", json_encode($dataarray));
