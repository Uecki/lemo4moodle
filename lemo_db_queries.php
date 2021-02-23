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

$querybarchart = "SELECT LOGS1.id, FROM_UNIXTIME (timecreated, '%d-%m-%Y') AS 'date', LOGS1.contextid, LOGS1.userid, LOGS1.component,
                            LOGS2.other, IF(LOGS1.component = 'mod_resource', RES.name, null) AS 'name'
                    FROM {logstore_standard_log} LOGS1
              INNER JOIN (SELECT contextid, other
                            FROM {logstore_standard_log}
                           WHERE " . $DB->sql_like('other', ':other') . "
                                AND " .  $DB->sql_compare_text('action') . " = " . $DB->sql_compare_text(':action') . ") LOGS2
                              ON LOGS1.contextid = LOGS2.contextid
              INNER JOIN {resource} RES ON LOGS1.objectid = RES.id
                   WHERE " .  $DB->sql_compare_text('LOGS1.action') . " = " . $DB->sql_compare_text(':action2') . "
                            AND " .  $DB->sql_compare_text('LOGS1.courseid') . " = " . $DB->sql_compare_text(':courseid');

//Query function parameters.
$params = ['other' => '%modulename%', 'action' => 'created', 'action2' => 'viewed', 'courseid' => $courseid];

// Perform SQL-Query.
$barchart = $DB->get_records_sql($querybarchart, $params);

unset($params);

//-------------------------------------------------------------------------------------------------------------------------------
// Debugging barchart queries.

//Barchart query w/o LIKE.
$querybarchartdebug1 = "SELECT LOGS1.id, FROM_UNIXTIME (timecreated, '%d-%m-%Y') AS 'date', LOGS1.contextid, LOGS1.userid, LOGS1.component,
                            LOGS1.other, IF(LOGS1.component = 'mod_resource', RES.name, null) AS 'name'
                    FROM {logstore_standard_log} LOGS1
              INNER JOIN {resource} RES ON LOGS1.objectid = RES.id
                   WHERE " .  $DB->sql_compare_text('LOGS1.action') . " = " . $DB->sql_compare_text(':action2') . "
                            AND " .  $DB->sql_compare_text('LOGS1.courseid') . " = " . $DB->sql_compare_text(':courseid');

//Query function parameters.
$params = ['action' => 'created', 'action2' => 'viewed', 'courseid' => $courseid];

// Perform SQL-Query.
$barchartdebug1 = $DB->get_records_sql($querybarchartdebug1, $params);

unset($params);

// Barchart query w/o LIKE and JOIN.
$querybarchartdebug2 = "SELECT LOGS1.id, FROM_UNIXTIME (timecreated, '%d-%m-%Y') AS 'date', LOGS1.contextid, LOGS1.userid, LOGS1.component
                    FROM {logstore_standard_log} LOGS1
                   WHERE " .  $DB->sql_compare_text('LOGS1.action') . " = " . $DB->sql_compare_text(':action2') . "
                            AND " .  $DB->sql_compare_text('LOGS1.courseid') . " = " . $DB->sql_compare_text(':courseid');

//Query function parameters.
$params = ['action2' => 'viewed', 'courseid' => $courseid];

// Perform SQL-Query.
$barchartdebug2 = $DB->get_records_sql($querybarchartdebug2, $params);

unset($params);

// Barchart query w/o JOIN.
$querybarchartdebug3 = "SELECT contextid, other FROM mdl_logstore_standard_log WHERE " . $DB->sql_like('other', ':other') . " AND " .  $DB->sql_compare_text('action') . " = " . $DB->sql_compare_text(':action') . " AND " .  $DB->sql_compare_text('courseid') . " = " . $DB->sql_compare_text(':courseid');

//Query function parameters.
$params = ['other' => '%modulename%', 'action' => 'created', 'courseid' => $courseid];

// Perform SQL-Query.
$barchartdebug3 = $DB->get_records_sql($querybarchartdebug3, $params);

unset($params);


// Query to check mdl_logstore_standard_log..
$querylogstore = "SELECT * FROM {logstore_standard_log} WHERE id = 1";

// Perform SQL-Query.
$logstore = $DB->get_records_sql($querylogstore);

// Query for SQL Mode.
$querysqlmode = "SELECT @@sql_mode";

// Perform SQL-Query.
$sqlmode = $DB->get_records_sql($querysqlmode);

// Query for SQL Mode.
$querycollation = "SHOW VARIABLES LIKE 'collation_database'";

// Perform SQL-Query.
$collation = $DB->get_records_sql($querycollation);

// Query for SQL Mode.
$querycharset = "SHOW VARIABLES LIKE 'character_set_database'";

// Perform SQL-Query.
$charset = $DB->get_records_sql($querycharset);


//-------------------------------------------------------------------------------------------------------------------------------

// Transform result of the query from Object to an array of Objects and make some minor changes to the data format.
$barchartdata = array();
foreach ($barchart as $b) {
    // If the content has no name in the "name" field, then the name has to be exctracted from the "other" field.
    if ($b->name == NULL) {
        $b->name = substr($b->other, strpos($b->other, '"name":"') + 8, -2);
    }
    $b->component = get_string($b->component, 'block_lemo4moodle');

    $barchartdata[] = $b;
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
