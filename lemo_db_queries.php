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

// SQL Query -> Linechart (date, hits, user counter).
$querylinechart = "SELECT id, timecreated AS date, COUNT(action) AS allhits,
                                COUNT(CASE WHEN " .  $DB->sql_compare_text('userid') . " = " . $DB->sql_compare_text(':userid') . "
                                THEN $userid END) AS ownhits
                           FROM {logstore_standard_log}
                          WHERE (" .  $DB->sql_compare_text('action') . " = " . $DB->sql_compare_text(':action') . "
                                AND " .  $DB->sql_compare_text('courseid') . " = " . $DB->sql_compare_text(':courseid') . ")
                       GROUP BY id
                       ORDER BY id ASC";

//Query function parameters.
$params = ['userid' => $userid, 'action' => 'viewed', 'courseid' => $courseid];

$linechart = $DB->get_records_sql($querylinechart, $params);

unset($params);

// Transform result of the query from Object to an array of Objects.
// Also, change UNIX timestamps to a string with the format dd-mm-yyyy.
$linechartdatatemp = array();

foreach ($linechart as $l) {

    // Assign timestamp in correct format.
    $datelinechart =  new DateTime("@$l->date", core_date::get_user_timezone_object());
    $l->date = $datelinechart->format('d-m-Y');
    $linechartdatatemp[] = $l;
    unset($datelinechart);
}

// Group dates and values by day.
$linechartdata = array();
$linechartdata[] = $linechartdatatemp[0];
$linechartdatacounter = 0;

for ($i = 1; $i < sizeof($linechartdatatemp); $i++) {

    if($linechartdatatemp[$i]->date == $linechartdata[$linechartdatacounter]->date) {
        $linechartdata[$linechartdatacounter]->allhits+=$linechartdatatemp[$i]->allhits;
        $linechartdata[$linechartdatacounter]->ownhits+=$linechartdatatemp[$i]->ownhits;
    } else {
        $linechartdatacounter++;
        $linechartdata[$linechartdatacounter] = $linechartdatatemp[$i];
    }

}


// Get the first recorded date of the datasets (used to indicate the first date of data-timespan in index.php).
$splitdate = explode("-", $linechartdata[0]->date);
$firstdateindex = $splitdate[0] . '.' . $splitdate[1] . '.' . $splitdate[2];


// SQL Query for bar chart data.

$querybarchart = "SELECT LOGS1.id, LOGS1.timecreated AS date, LOGS1.contextid, LOGS1.userid, LOGS1.component
                    FROM {logstore_standard_log} LOGS1
                   WHERE " . $DB->sql_like('LOGS1.component', ':component') . "
                            AND " .  $DB->sql_compare_text('LOGS1.action') . " = " . $DB->sql_compare_text(':action2') . "
                            AND " .  $DB->sql_compare_text('LOGS1.courseid') . " = " . $DB->sql_compare_text(':courseid') . "
                            AND LOGS1.objecttable IS NOT NULL
                            AND " .  $DB->sql_compare_text('target') . " = " . $DB->sql_compare_text(':target');

//Query function parameters.
$params = ['component' => 'mod%', 'action2' => 'viewed', 'courseid' => $courseid, 'target' => 'course_module'];

// Perform SQL-Query.
$barchart = $DB->get_records_sql($querybarchart, $params);

unset($params);

// Get module information of the current course to later complement the query.
GLOBAL $COURSE;
// Use moodle function get_fast_modinfo().
$modinfo = get_fast_modinfo($COURSE);
$modulesarray = array();


// Add name, modulename and contextid of each object in the course to an associative array with the time an object was added to the course as key.
foreach ($modinfo->get_cms() as $cminfo) {
    $modulesarray[] = array('name' => $cminfo->name, 'module' => $cminfo->modname, 'contextid' => $cminfo->context->id);
}

// Transform result of the query from Object to an array of Objects.
$barchartdatatemp = array();
foreach ($barchart as $b) {
    $barchartdatatemp[] = $b;
}

// Assign the objectname to each result of the barchart query by comparing the contextids from the
// objectlist ($modulesarray) with the contextid of each query result.
// Also, change UNIX timestamps to a string with the format dd-mm-yyyy.
foreach($barchartdatatemp as $bd) {
    // Assign objectname.
    $found = false;
    foreach($modulesarray as $ma) {
        if($ma['contextid'] == $bd->contextid) {
            $bd->name = $ma['name'];
            $found = true;
            break;
        }
    }

    if($found == false) {
        $bd->contextid = 0;
    }

    // Assign timestamp in correct format.
    $datebarchart =  new DateTime("@$bd->date", core_date::get_user_timezone_object());
    $bd->date = $datebarchart->format('d-m-Y');
    unset($datebarchart);

    // Replace the component (module) name with the string from the language file.
    $bd->component = substr_replace($bd->component, '', 0, 4);
    $bd->component = get_string($bd->component, 'block_lemo4moodle');
    if(strpos($bd->component, '[[') !== false) {
        $bd->component = substr_replace($bd->component, '', 0, 2);
        $bd->component = substr_replace($bd->component, '', strlen($bd->component)-2, strlen($bd->component));
    }
}

// Filter out any not assigned/no longer existing objects.
$barchartdata = array();
foreach($barchartdatatemp as $bd) {
    if($bd->contextid != 0) {
        $barchartdata[] = $bd;
    }
}


// Query for heatmap.
$queryheatmap = "SELECT  id, timecreated, COUNT(action) AS allHits,
                            COUNT(CASE WHEN " .  $DB->sql_compare_text('userid') . " = " . $DB->sql_compare_text(':userid') . "
                            THEN $userid END) AS ownhits
                       FROM {logstore_standard_log}
                      WHERE (" .  $DB->sql_compare_text('action') . " = " . $DB->sql_compare_text(':action') . "
                            AND " .  $DB->sql_compare_text('courseid') . " = " . $DB->sql_compare_text(':courseid') . ")
                     GROUP BY id";

//Query function parameters.
$params = ['userid' => $userid, 'action' => 'viewed', 'courseid' => $courseid];

$heatmap = $DB->get_records_sql($queryheatmap, $params);

unset($params);

// Transform result of the query from Object to array of Objects get real date from timestamp.
$heatmaptdata = array();
foreach ($heatmap as $h) {
    $dateheatmap =  new DateTime("@$h->timecreated", core_date::get_user_timezone_object());
    $h->date = $dateheatmap->format('d-m-Y');
    $h->weekday = $dateheatmap->format('l');
    $h->hour = $dateheatmap->format('G');;
    $heatmapdata[] = $h;
    unset($dateheatmap);
}


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
