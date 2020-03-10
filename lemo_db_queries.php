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
 * @copyright  2020 Margarita Elkina
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

// Alternative: exchange overall actions with only Logins --> WHERE action='loggedin'.
$result = $DB->get_records_sql($querylinechart);

// Create array to save fetched results | create Object.
$activity = array();
$counter = 0;

/**
 * Class that holds the results of the linechart query.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Margarita Elkina
 * @license    http:// www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Activity {
    /** @var string This variable stores the date. */
    public $date;
    /** @var string This variable stores the number of accesses. */
    public $accesses;
    /** @var string This variable stores the number of users that did an action. */
    public $user;
    /** @var string This variable stores the number of the users own actions. */
    public $ownhits;
    /** @var string This variable stores the timestamp. */
    public $timestamp;
}

// Fetch results.
foreach ($result as $row) {
    if (!empty($row->allhits)) {
        // New Object.
        ${"activity".$counter} = new Activity();

        // Split date.
        $parts = explode("-", $row->date);
        $day = $parts[0];
        $month = $parts[1] - 1;
        $jahr = $parts[2];
        $fulldate = $jahr.', '.$month.', '.$day;

        ${"activity".$counter}->date = $fulldate;

        ${"activity".$counter}->accesses = $row->allhits;

        ${"activity".$counter}->user = $row->users;

        // Create timestamp.
        $pretimestamp = $row->date.' 08:00:00';
        $createtimestamp = strtotime($pretimestamp);

        ${"activity".$counter}->timestamp = $createtimestamp;

        ${"activity".$counter}->ownhits = $row->ownhits;

        // Write created object to array.
        $activity[] = ${"activity".$counter};
    }
    $counter += 1;
}

// Loop activity array and create data for non-existing days.
$finallinechartobject = array();
$nextday = $activity[0]->timestamp;
$counter = 1;

foreach ($activity as $a) {

    if ($nextday != $a->timestamp) {
        // ZERO DATA.
        $dateyear = date("Y", $nextday);
        $datemonth = date("n", $nextday);
        $datemonth -= 1;
        $dateday = date("d", $nextday);
        $datefinal = $dateyear.', '.$datemonth.', '.$dateday;
        $nextday = strtotime('+1 day', $nextday);
        $boolean = true;

        // Using Objects.
        ${"final".$counter} = new Activity();
        ${"final".$counter}->date = $datefinal;
        ${"final".$counter}->accesses = 0;
        ${"final".$counter}->user = 0;
        ${"final".$counter}->timestamp = $nextday;
        ${"final".$counter}->ownhits = 0;
        $finallinechartobject[] = ${"final".$counter};
        $counter += 1;

        while ($boolean == true) {
            // DB DATA.
            if ($nextday == $a->timestamp) {
                $nextday = strtotime('+1 day', $a->timestamp);
                $boolean = false;

                // Using object.
                ${"final".$counter} = new Activity();
                ${"final".$counter}->date = $a->date;
                ${"final".$counter}->accesses = $a->accesses;
                ${"final".$counter}->user = $a->user;
                ${"final".$counter}->timestamp = $a->timestamp;
                ${"final".$counter}->ownhits = $a->ownhits;
                $finallinechartobject[] = ${"final".$counter};
                $counter += 1;


            } else {
                // ZERO DATA.
                $dateyear = date("Y", $nextday);
                $datemonth = date("n", $nextday);
                $datemonth -= 1;
                $dateday = date("d", $nextday);
                $datefinal = $dateyear.', '.$datemonth.', '.$dateday;
                $nextday = strtotime('+1 day', $nextday);
                $boolean = true;

                // Using Objects.

                ${"final".$counter} = new Activity();
                ${"final".$counter}->date = $datefinal;
                ${"final".$counter}->accesses = 0;
                ${"final".$counter}->user = 0;
                ${"final".$counter}->timestamp = $nextday;
                ${"final".$counter}->ownhits = 0;
                $finallinechartobject[] = ${"final".$counter};
                $counter += 1;
            }
        }
        continue;
    }
    // DB DATA.

    // Using object.
    ${"final".$counter} = new Activity();
    ${"final".$counter}->date = $a->date;
    ${"final".$counter}->accesses = $a->accesses;
    ${"final".$counter}->user = $a->user;
    ${"final".$counter}->timestamp = $a->timestamp;
    ${"final".$counter}->ownhits = $a->ownhits;
    $finallinechartobject[] = ${"final".$counter};

    $nextday = strtotime('+1 day', $a->timestamp);
    $counter += 1;
}

// Create data string for line chart.
$f = 0;
$length = count($finallinechartobject);
$linechart = "";
$linechartdataarray = array();

foreach ($finallinechartobject as $fo) {
    if ($f < $length - 1) {
        $linechart .= "[new Date(".$fo->date."), ".$fo->accesses.", ".$fo->ownhits.", ".$fo->user."],";
    }
    if ($f == $length - 1) {
        $linechart .= "[new Date(".$fo->date."), ".$fo->accesses.", ".$fo->ownhits.", ".$fo->user."]";
    }
    $linechartdataarray[] = array("new Date(".$fo->date.")", $fo->accesses, $fo->ownhits, $fo->user, $fo->timestamp);
    $f++;
}

// Get the first recorded date of the datasets (used to indicate the first date of data-timespan in index.php).
preg_match_all('/\d+/', $linechartdataarray[0][0], $matches);
$firstdateindex = $matches[0][2].'.'.(intval($matches[0][1]) + 1).'.'.$matches[0][0]; // Month needs to be augmented by 1.

// SQL Query for bar chart data.

$querybarchart = "SELECT RESOURCE.id, name, counter_hits, counter_user
                    FROM (SELECT contextid, courseid, objectid, userid, count(objectid) AS counter_hits, count(DISTINCT userid) AS counter_user
                            FROM mdl_logstore_standard_log
                           WHERE target = 'course_module'
                        GROUP BY courseid, objectid) AS LOGS
                    JOIN (SELECT id FROM mdl_course) AS COURSE ON LOGS.courseid = COURSE.id
                    JOIN (SELECT mdl_resource.id, name, timemodified FROM mdl_resource) AS RESOURCE ON LOGS.objectid = RESOURCE.id WHERE courseid = '".$courseid."'";


// Perform SQL-Query.
$barchart = $DB->get_records_sql($querybarchart);

// Create barchart data.
$j = 1;
$leng = count($barchart);
$barchartdataarray = array();
$barchartdata = "[['".get_string('barchart_xlabel', 'block_lemo4moodle')."', '".get_string('barchart_ylabel',
    'block_lemo4moodle')."', '".get_string('barchart_users', 'block_lemo4moodle')."'],";

foreach ($barchart as $bar) {
    if ($j < $leng ) {
        $barchartdata .= "['".$bar->name."', ".$bar->counter_hits.", ".$bar->counter_user."],";
    }
    if ($j == $leng ) {
        $barchartdata .= "['".$bar->name."', ".$bar->counter_hits.", ".$bar->counter_user."]]";
    }
    $barchartdataarray[] = array($bar->name, $bar->counter_hits, $bar->counter_user);
    $j++;
}


// Query for heatmap. Only minor changes to activity chart query.

$queryheatmap = "SELECT  id, timecreated, FROM_UNIXTIME(timecreated, '%W') AS 'weekday', FROM_UNIXTIME(timecreated, '%k') AS 'hour',
    COUNT(action) AS 'allHits',  COUNT(case when userid = $userid then $userid end) AS 'ownhits'
                         FROM {logstore_standard_log}
                        WHERE (courseid = '".$courseid."')
                     GROUP BY timecreated"; // Group by hour.

$heatmap = $DB->get_records_sql($queryheatmap);

// Create heatmap data.
$timespan;
$heatmapdata = "[";
$counterweekday = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

// Array for total number of weekday actions.
$totalhits = array(
    "Monday"  => 0,
    "Tuesday"  => 0,
    "Wednesday"  => 0,
    "Thursday"  => 0,
    "Friday"  => 0,
    "Saturday"  => 0,
    "Sunday"  => 0,
);

// Array for total  number of own weekday actions.
$totalownhits = array(
    "Monday"  => 0,
    "Tuesday"  => 0,
    "Wednesday"  => 0,
    "Thursday"  => 0,
    "Friday"  => 0,
    "Saturday"  => 0,
    "Sunday"  => 0,
);

{// Array to assign the query results (inside curly braces to hide block).
$weekdays = array(
    "Monday" => array(
        "0to6" => array(
            "all" => array(
                "col"  => 0,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 1,
                "value" => 0,
            ),
        ),
        "6to12" => array(
            "all" => array(
                "col"  => 2,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 3,
                "value" => 0,
            ),
        ),

        "12to18" => array(
            "all" => array(
                "col"  => 4,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 5,
                "value" => 0,
            ),
        ),

        "18to24" => array(
            "all" => array(
                "col"  => 6,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 7,
                "value" => 0,
            ),
        ),
        "row" => 0,
    ),
    "Tuesday" => array(
        "0to6" => array(
            "all" => array(
                "col"  => 0,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 1,
                "value" => 0,
            ),
        ),
        "6to12" => array(
            "all" => array(
                "col"  => 2,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 3,
                "value" => 0,
            ),
        ),

        "12to18" => array(
            "all" => array(
                "col"  => 4,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 5,
                "value" => 0,
            ),
        ),

        "18to24" => array(
            "all" => array(
                "col"  => 6,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 7,
                "value" => 0,
            ),
        ),
        "row" => 1,
    ),
    "Wednesday" => array(
        "0to6" => array(
            "all" => array(
                "col"  => 0,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 1,
                "value" => 0,
            ),
        ),
        "6to12" => array(
            "all" => array(
                "col"  => 2,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 3,
                "value" => 0,
            ),
        ),

        "12to18" => array(
            "all" => array(
                "col"  => 4,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 5,
                "value" => 0,
            ),
        ),

        "18to24" => array(
            "all" => array(
                "col"  => 6,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 7,
                "value" => 0,
            ),
        ),
        "row" => 2,
    ),
    "Thursday" => array(
        "0to6" => array(
            "all" => array(
                "col"  => 0,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 1,
                "value" => 0,
            ),
        ),
        "6to12" => array(
            "all" => array(
                "col"  => 2,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 3,
                "value" => 0,
            ),
        ),

        "12to18" => array(
            "all" => array(
                "col"  => 4,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 5,
                "value" => 0,
            ),
        ),

        "18to24" => array(
            "all" => array(
                "col"  => 6,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 7,
                "value" => 0,
            ),
        ),
        "row" => 3,
    ),
    "Friday" => array(
        "0to6" => array(
            "all" => array(
                "col"  => 0,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 1,
                "value" => 0,
            ),
        ),
        "6to12" => array(
            "all" => array(
                "col"  => 2,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 3,
                "value" => 0,
            ),
        ),

        "12to18" => array(
            "all" => array(
                "col"  => 4,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 5,
                "value" => 0,
            ),
        ),

        "18to24" => array(
            "all" => array(
                "col"  => 6,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 7,
                "value" => 0,
            ),
        ),
        "row" => 4,
    ),
    "Saturday" => array(
        "0to6" => array(
            "all" => array(
                "col"  => 0,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 1,
                "value" => 0,
            ),
        ),
        "6to12" => array(
            "all" => array(
                "col"  => 2,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 3,
                "value" => 0,
            ),
        ),

        "12to18" => array(
            "all" => array(
                "col"  => 4,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 5,
                "value" => 0,
            ),
        ),

        "18to24" => array(
            "all" => array(
                "col"  => 6,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 7,
                "value" => 0,
            ),
        ),
        "row" => 5,
    ),
    "Sunday" => array(
        "0to6" => array(
            "all" => array(
                "col"  => 0,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 1,
                "value" => 0,
            ),
        ),
        "6to12" => array(
            "all" => array(
                "col"  => 2,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 3,
                "value" => 0,
            ),
        ),

        "12to18" => array(
            "all" => array(
                "col"  => 4,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 5,
                "value" => 0,
            ),
        ),

        "18to24" => array(
            "all" => array(
                "col"  => 6,
                "value" => 0,
            ),
            "own" => array(
                "col"  => 7,
                "value" => 0,
            ),
        ),
        "row" => 6,
    ),
);
}

foreach ($heatmap as $heat) {

    // Link timespan to column in heatmap.
    if ((int)$heat->hour >= 0  && (int)$heat->hour < 6) {
        $timespan = "0to6";
    } else if ((int)$heat->hour >= 6  && (int)$heat->hour < 12) {
        $timespan = "6to12";
    } else if ((int)$heat->hour >= 12  && (int)$heat->hour < 18) {
        $timespan = "12to18";
    } else if ((int)$heat->hour >= 18  && (int)$heat->hour < 24) {
        $timespan = "18to24";
    }

    // Data for specific day.
    $weekdays[$heat->weekday][$timespan]["all"]["value"] += (int)$heat->allhits;
    $weekdays[$heat->weekday][$timespan]["own"]["value"] += (int)$heat->ownhits;

    // Data for overall clicks.
    $totalhits[$heat->weekday] += (int)$heat->allhits;
    $totalownhits[$heat->weekday] += (int)$heat->ownhits;

}

    // Put data of each weekdayfield into suitable format for the chart.
$counter = 0;
while ($counter <= 6) {

    // Data for index.php.
    $daydata = "[".$weekdays[$counterweekday[$counter]]['0to6']['all']['value'].", ".
        $weekdays[$counterweekday[$counter]]['0to6']['own']['value'].", ".
        $weekdays[$counterweekday[$counter]]['6to12']['all']['value'].", ".
        $weekdays[$counterweekday[$counter]]['6to12']['own']['value'].", ".
        $weekdays[$counterweekday[$counter]]['12to18']['all']['value'].", ".
        $weekdays[$counterweekday[$counter]]['12to18']['own']['value'].", ".
        $weekdays[$counterweekday[$counter]]['18to24']['all']['value'].", ".
        $weekdays[$counterweekday[$counter]]['18to24']['own']['value'].", ".
        $totalhits[$counterweekday[$counter]].", ".
        $totalownhits[$counterweekday[$counter]].", ".
        round(($totalhits[$counterweekday[$counter]] / 4.0), 2).", ".
        round(($totalownhits[$counterweekday[$counter]] / 4.0), 2)."]";

    if ($counter < 6) {
        $daydata .= ", ";
    }

    $heatmapdata .= $daydata;

    $counter++;
}

$heatmapdata .= "]";


// Use barchart query for treemap.
$treemap = $barchart;


// Create treemap data.
$color = - 50; // Variable for node color.
$i = 1;
$nodetitle = 'Global'; // Variable for node title.
$lengtree = count($treemap);
$treemapdataarray = array();
$treemapdata =
    "[['Name', 'Parent', 'Size', 'Color'],
        ['".get_string('treemap_global', 'block_lemo4moodle')."', null, 0, 0],";

foreach ($treemap as $tree) {

    // If-clause for node title. (Maybe) To be expanded for forum, chat and assignments.
    if ($i < $lengtree ) {
        $treemapdata .= "['".$tree->name."', '".$nodetitle."', ".$tree->counter_hits.", ".$color."],";
    }
    if ($i == $lengtree ) {
        $treemapdata .= "['".$tree->name."', '".$nodetitle."', ".$tree->counter_hits.", ".$color."]]";
    }
    $treemapdataarray[] = array($tree->name, $nodetitle, $tree->counter_hits, $color);
    $i++;
    $color = $color + 10;
}

// Create dataarray.
// Data as JSON [activityData[date, overallHits, ownhits, users], barchartdata[name, hits, users],
// treemapdata[name, title, hits, color(as int)]].
$dataarray = array();
$dataarray[] = $linechartdataarray;
$dataarray[] = $barchartdataarray;
$dataarray[] = $heatmapdata;
$dataarray[] = $treemapdataarray;
$dataarray[] = $heatmap;

// Encode dataarray as JSON !JSON_NUMERIC_CHECK.
// Gets encoded only to be decoded in lemo_create_html.php, probably not necessary.
$alldata = json_encode($dataarray, JSON_NUMERIC_CHECK);
