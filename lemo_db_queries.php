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

// SQL Query -> ActivityChart (date, hits, user counter).
$querylinechart = "SELECT FROM_UNIXTIME (timecreated, '%d-%m-%Y') AS 'date', COUNT(action) AS 'allHits',
                                COUNT(DISTINCT userid) AS 'users', COUNT(case when userid = $userid then $userid end) AS 'ownhits'
                           FROM {logstore_standard_log}
                          WHERE (action = 'viewed' AND courseid = '".$courseid."')
                       GROUP BY FROM_UNIXTIME (timecreated, '%y-%m-%d')
                       ORDER BY 'date'";

$linechart = $DB->get_records_sql($querylinechart);

// Transform result of the query from Object to an array of Objects.
$linechartdata = array();
foreach ($linechart as $l) {
    $linechartdata[] = $l;
}


// Get the first recorded date of the datasets (used to indicate the first date of data-timespan in index.php).
$splitdate = explode("-", $linechartdata[0]->date);
$firstdateindex = $splitdate[0] . '.' . $splitdate[1] . '.' . $splitdate[2];


// SQL Query for bar chart data.

$querybarchart = "SELECT LOGS.id, FROM_UNIXTIME (timecreated, '%d-%m-%Y') AS 'date', LOGS.contextid AS contextid, LOGS.userid
                        AS userid, LOGS.contextid, LOGS.component, LOGS2.other, IF(LOGS.component = 'mod_resource', RES.name, null) AS name
                    FROM {logstore_standard_log} AS LOGS
              INNER JOIN (SELECT contextid, other FROM {logstore_standard_log} WHERE other LIKE '{\"modulename\"%' AND action = 'created')
                        AS LOGS2 ON LOGS.contextid = LOGS2.contextid
              INNER JOIN {resource} AS RES ON LOGS.objectid = RES.id
                   WHERE action = 'viewed' AND LOGS.courseid ="  . $courseid;


// Perform SQL-Query.
$barchart = $DB->get_records_sql($querybarchart);

// Transform result of the query from Object to an array of Objects and make some minor changes to the data format.
$barchartdata = array();
foreach ($barchart as $b) {
    // If the content ha no name in the "name" field, then the name has to be exctracted from the "other" field.
    if ($b->name == NULL) {
        $b->name = substr($b->other, strpos($b->other, '"name":"') + 8, -2);
    }
    $b->component = get_string($b->component, 'block_lemo4moodle');

    $barchartdata[] = $b;
}

/*
// Create barchart data.
$j = 1; // Counter.
$leng = count($barchart);
/* Currently not necessary.
// Array that stores the info needed to open files in moodle.
$barchartfileinfo = array();
*/
/*
$barchartdataarray = array();
$barchartdataarray[] = array(get_string('barchart_xlabel', 'block_lemo4moodle'), get_string('barchart_ylabel',
    'block_lemo4moodle'), get_string('barchart_users', 'block_lemo4moodle'), get_string('barchart_module', 'block_lemo4moodle'));

$barchartdata = "[['".get_string('barchart_xlabel', 'block_lemo4moodle')."', '".get_string('barchart_ylabel',
    'block_lemo4moodle')."', '".get_string('barchart_users', 'block_lemo4moodle').", ".get_string('barchart_users', 'block_lemo4moodle')."']";

//Check, if there are no objects in the moodle course.
if ($leng != 0){
    $barchartdata .= ", ";
} else {
    $barchartdata .= "]";
}

foreach ($barchart as $bar) {
    // Get the name of the module from the database entry "other".
    //$bar->other = str_replace
    $contentname;
    if ($bar->name == NULL) {
        $contentname = substr($bar->other, strpos($bar->other, '"name":"') + 8, -2);
    } else {
        $contentname = $bar->name;
    }

    //Variable that stores the module type of the content.
    $contentmodule = get_string($bar->component, 'block_lemo4moodle');

    if ($j < $leng ) {
        $barchartdata .= "['".$contentname."', ".$bar->counter_hits.", ".$bar->counter_user. ", " .$contentmodule. "], ";
    }
    if ($j == $leng ) {
        $barchartdata .= "['".$contentname."', ".$bar->counter_hits.", ".$bar->counter_user. ", " .$contentmodule."]]";
    }
    // Fileinfo currently not needed.
    // $barchartfileinfo[] = array($bar->other, $bar->contextid, $bar->component, $bar->filearea, $bar->itemid, $bar->filename);
    $barchartdataarray[] = array($contentname, $bar->counter_hits, $bar->counter_user, $contentmodule);
    $j++;
}
*/


// Query for heatmap. Only minor changes to activity chart query.

$queryheatmap = "SELECT  id, timecreated, FROM_UNIXTIME(timecreated, '%W') AS 'weekday', FROM_UNIXTIME(timecreated, '%k') AS 'hour',
                            COUNT(action) AS 'allHits',  COUNT(case when userid = $userid then $userid end) AS 'ownhits'
                         FROM {logstore_standard_log}
                        WHERE (courseid = '".$courseid."')
                     GROUP BY timecreated"; // Group by hour.

$heatmap = $DB->get_records_sql($queryheatmap);

// Transform result of the query from Object to array of Objects.
$heatmaptdata = array();
foreach ($heatmap as $h) {
    $h->date = date("d-m-Y", $h->timecreated);
    $heatmapdata[] = $h;
}

/* Treemap currently not in use/implemented.

// Use barchart query for treemap.
$treemap = $barchart;


// Create treemap data.
$color = - 50; // Variable for node color.
$i = 1;
$nodetitle = 'Global'; // Variable for node title.
$lengtree = count($treemap);
$treemapdataarray = array();
$treemapdataarray[] = array(get_string('treemap_global', 'block_lemo4moodle'), null, 0, 0);
$treemapdata =
    "[['Name', 'Parent', 'Size', 'Color'],
        ['".get_string('treemap_global', 'block_lemo4moodle')."', null, 0, 0]";

//Check, if there are no objects in the moodle course.
if ($leng != 0){
    $treemapdata .= ", ";
} else {
    $treemapdata .= "]";
}

foreach ($treemap as $tree) {
    // If-clause for node title. (Maybe) To be expanded for forum, chat and assignments.
    if ($i < $lengtree ) {
        $treemapdata .= "['".$tree->name."', '".$nodetitle."', ".$tree->counter_hits.", ".$color."], ";
    }
    if ($i == $lengtree ) {
        $treemapdata .= "['".$tree->name."', '".$nodetitle."', ".$tree->counter_hits.", ".$color."]]";
    }
    $treemapdataarray[] = array($tree->name, $nodetitle, $tree->counter_hits, $color);
    $i++;
    $color = $color + 10;
}

*/

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
