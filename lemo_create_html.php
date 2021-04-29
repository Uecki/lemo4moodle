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
 * This file creates the downloadable lemo4mooodle .html files.
 *
 * After clicking on the "Download" button on index.php, this file is executed.
 * Depending on what the user chose, either only one chart or all charts are included
 * in the final downloaded file.
 * In the end, the content of the file is echoed.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Finn Ueckert, Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

// Login to current course.
$courseid  = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
require_login($course);

// Get courseid, userid and alldata (encoded data-arrays in JSON-format).
global $USER, $COURSE;
$courseid = $COURSE->id;
$userid = $USER->id;

$alldata;
$linechartdata;
$barchartdata;
$heatmapdata;

// Check, if the data received comes from two merged files or from the index.php.
if(!isset($_POST["mergeData"]) || $_POST["mergeData"] == "") {
    $alldata = json_decode($_POST["data"], true);
    $linechartdata = json_encode($alldata[0], JSON_NUMERIC_CHECK);
    $barchartdata = json_encode($alldata[1], JSON_NUMERIC_CHECK);
    $heatmapdata = json_encode($alldata[2], JSON_NUMERIC_CHECK);
} else {
    $alldata = json_decode($_POST["mergeData"], true);
    $linechartdata = json_encode($alldata[0], JSON_NUMERIC_CHECK);
    $barchartdata = json_encode($alldata[1], JSON_NUMERIC_CHECK);
    $barchartdata = str_replace("\\\\", "\\", $barchartdata);
    $heatmapdata = json_encode($alldata[2], JSON_NUMERIC_CHECK);
}

// Get today's date.
$today = date("d.m.y");

// Get date for filename.
$todayfilename = date("Y_m_d");

// Check timespan of logdata.
$firstdate = 0;
$lastdate = 0;

// Perform various checks to find the earliest dataset.
// This is necessary, because the timespan of the logdata is displayed and datasets can be downloaded an merged separately from each other.
foreach($alldata as $value) {
    if (isset($value[0])) {
        if($firstdate == 0
                || ($firstdate != 0
                    && $firstdate > strtotime($value[0]["date"])
                    && (strtotime($value[0]["date"]) !== false))) {
            $firstdate = strtotime($value[0]["date"]);
        }

        if($lastdate == 0
                || ($lastdate != 0
                    && $lastdate < strtotime($value[sizeof($value)-1]["date"])
                    && strtotime($value[sizeof($value)-1]["date"]) !== false)) {
            $lastdate = strtotime($value[sizeof($value)-1]["date"]);
        }
    }
}

$firstdate = date("d.m.Y", $firstdate);
$lastdate = date("d.m.Y", $lastdate);


// Get the last recorded date of the datasets.
if (!isset($_POST["mergeData"]) || $_POST["mergeData"] == "") {
    $lastdate = date("d.m.Y");
}


// Initializing content variable.
$content = "";

// Setting the content, depending on if the users wants all charts or only one.
// Notice: Indentation coding style incorrect for better readability.
if ($_POST['allCharts'] == 'true') {
    // HTML part.
    $content =
    '<!DOCTYPE html>
    <html lang="'.get_string('lang', 'block_lemo4moodle').'">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>'.get_string('pluginname', 'block_lemo4moodle').'</title>

        <!-- Datepicker jQuery.-->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

        <!-- Materialize CSS Framework - minified CSS. -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">

        <!-- Google Icons. -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

        <!-- styles.css. -->
        <style>'.file_get_contents('styles.css').'</style>

    </head>
    <body>
    <!-- Header. -->
    <div class="block_lemo4moodle">
    <div class="container-fluid">
        <nav>
          <div class="nav-wrapper">
              <a onClick="window.location.reload()" class="brand-logo">
                  <i class="material-icons medium">insert_chart</i>'.get_string('pluginname', 'block_lemo4moodle').'</a>
          </div>
        </nav>
        <!-- Tabs. -->
        <div class="row">
            <div class="col s12">
                <ul class="tabs" id="tabs">
                    <li class="tab disabled">
                        <a href="#">'.get_string('logdata', 'block_lemo4moodle').$firstdate.' - '.$lastdate.'</a>
                    </li>
                    <li class="tab" id="tab_barChart">
                        <a class="active" id="tab1" href="#chart1">Barchart</a>
                    </li>
                    <li class="tab" id="tab_activityChart">
                        <a id="tab2" href="#chart2">Activity Chart</a>
                    </li>
                    <li class="tab" id="tab_heatMap">
                        <a id="tab3" href="#chart3">Heatmap</a>
                    </li>
                </ul>
            </div>
            <!-- Barchart. -->
            <div id="chart1" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                      <div id="barchart" class="block_lemo4moodle-chart"></div>
                    </div>
                    <div id="options" class="col s3">
                        <div class="row">
                            <div class="input-field col s12">
                                <p>'.get_string('selectStart', 'block_lemo4moodle').'</p>
                                <select id="barchart_select_module">
                                    <option value="all" selected>'.get_string('selectAll', 'block_lemo4moodle').'</option>
                                </select>
                                <br>
                                <div class="divider"></div>
                                <p>'.get_string('filter', 'block_lemo4moodle').'</p>
                                <input placeholder="Beginn" type="text" class="datepick " id="datepicker_1">
                                <input placeholder="Ende" type="text" class="datepick " id="datepicker_2">
                                <button class="btn waves-effect waves-light grey darken-3 button"
                                    type="submit" name="action" id="dp_button_1">'.
                                    get_string('update', 'block_lemo4moodle').'</button>
                                <button class="btn waves-effect waves-light grey darken-3 button"
                                    type="submit" name="action" id="rst_btn_1">'.
                                    get_string('reset', 'block_lemo4moodle').'</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Linechart/activity chart. -->
            <div id="chart2" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <div id="linechart" class="block_lemo4moodle-chart"></div>
                    </div>
                        <div id="options" class="col s3">
                            <div class="row">
                                <div class="input-field col s12">
                                    <div class="divider"></div>
                                    <p>'.get_string('filter', 'block_lemo4moodle').'</p>
                                    <input placeholder="Beginn" type="text" class="datepick " id="datepicker_3">
                                    <input placeholder="Ende" type="text" class="datepick " id="datepicker_4">
                                    <button class="btn waves-effect waves-light grey darken-3 button"
                                        type="submit" name="action" id="dp_button_2">'.
                                        get_string('update', 'block_lemo4moodle').'</button>
                                    <button class="btn waves-effect waves-light grey darken-3 button"
                                        type="submit" name="action" id="rst_btn_2">'.
                                        get_string('reset', 'block_lemo4moodle').'</button>
                                <div class="divider"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Heatmap. -->
            <div id="chart3" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <div  id="heatmap" class="block_lemo4moodle-chart"></div>
                    </div>
                    <div id="options" class="col s3">
                        <div class="row">
                            <div class="input-field col s12">
                                <div class="divider"></div>
                                <p>'.get_string('filter', 'block_lemo4moodle').'</p>
                                <input placeholder="Beginn" type="text" class="datepick " id="datepicker_5">
                                <input placeholder="Ende" type="text" class="datepick " id="datepicker_6">
                                <button class="btn waves-effect waves-light grey darken-3 button"
                                    type="submit" name="action" id="dp_button_3">'.
                                    get_string('update', 'block_lemo4moodle').'</button>
                                <button class="btn waves-effect waves-light grey darken-3 button"
                                    type="submit" name="action" id="rst_btn_3">'.
                                    get_string('reset', 'block_lemo4moodle').'</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="jsvariables">
            <!-- Language-string variables made accessible for JS. -->
            <input type="hidden" value="' .
                get_string("selectAll", "block_lemo4moodle") . '" id="selectAll">
            <!-- Barchart. -->
            <input type="hidden" value="' .
                get_string("barchart_module", "block_lemo4moodle") . '" id="barchartModule">
            <input type="hidden" value="' .
                get_string("barchart_title", "block_lemo4moodle") . '" id="barchartTitle">
            <input type="hidden" value="' .
                get_string("barchart_xlabel", "block_lemo4moodle") . '" id="barchartXLabel">
            <input type="hidden" value="' .
                get_string("barchart_ylabel", "block_lemo4moodle") . '" id="barchartYLabel">
            <!-- Linechart. -->
            <input type="hidden" value="' .
                get_string("linechart_colDate", "block_lemo4moodle") . '" id="linechartColDate">
            <input type="hidden" value="' .
                get_string("linechart_colAccess", "block_lemo4moodle") . '" id="linechartColAccess">
            <input type="hidden" value="' .
                get_string("linechart_colOwnAccess", "block_lemo4moodle") . '" id="linechartColOwnAccess">
            <input type="hidden" value="' .
                get_string("linechart_colUser", "block_lemo4moodle") . '" id="linechartColUser">
            <input type="hidden" value="' .
                get_string("linechart_colMissingData", "block_lemo4moodle") . '" id="linechartColMissingData">
            <input type="hidden" value="' .
                get_string("linechart_title", "block_lemo4moodle") . '" id="linechartTitle">
            <!--Heatmap.  -->
            <input type="hidden" value="' .
                get_string("heatmap_title", "block_lemo4moodle") . '" id="heatmapTitle">
            <input type="hidden" value="' .
                get_string("heatmap_all", "block_lemo4moodle") . '" id="heatmapAll">
            <input type="hidden" value="' .
                get_string("heatmap_own", "block_lemo4moodle") . '" id="heatmapOwn">
            <input type="hidden" value="' .
                get_string("heatmap_overall", "block_lemo4moodle") . '" id="heatmapOverall">
            <input type="hidden" value="' .
                get_string("heatmap_average", "block_lemo4moodle") . '" id="heatmapAverage">
            <input type="hidden" value="' .
                get_string("heatmap_monday", "block_lemo4moodle") . '" id="heatmapMonday">
            <input type="hidden" value="' .
                get_string("heatmap_tuesday", "block_lemo4moodle") . '" id="heatmapTuesday">
            <input type="hidden" value="' .
                get_string("heatmap_wednesday", "block_lemo4moodle") . '" id="heatmapWednesday">
            <input type="hidden" value="' .
                get_string("heatmap_thursday", "block_lemo4moodle") . '" id="heatmapThursday">
            <input type="hidden" value="' .
                get_string("heatmap_friday", "block_lemo4moodle") . '" id="heatmapFriday">
            <input type="hidden" value="' .
                get_string("heatmap_saturday", "block_lemo4moodle") . '" id="heatmapSaturday">
            <input type="hidden" value="' .
                get_string("heatmap_sunday", "block_lemo4moodle") . '" id="heatmapSunday">
            <!-- Treemap. -->
            <input type="hidden" value="' .
                get_string("treemap_title", "block_lemo4moodle") . '" id="treemapTitle">
            <input type="hidden" value="' .
                get_string("treemap_clickCount", "block_lemo4moodle") . '" id="treemapClickCount">
            <!-- View. -->
            <input type="hidden" value="' .
                get_string('view_checkSelection', 'block_lemo4moodle') . '" id="viewCheckSelection">
            <input type="hidden" value="' .
                get_string("view_dialogThis", "block_lemo4moodle") . '" id="viewDialogThis">
            <input type="hidden" value="' .
                get_string("view_dialogAll", "block_lemo4moodle") . '" id="viewDialogAll">
            <input type="hidden" value="' .
                get_string("view_file", "block_lemo4moodle") . '" id="viewFile">
            <input type="hidden" value="' .
                get_string("view_timespan", "block_lemo4moodle") . '" id="viewTimespan">
            <input type="hidden" value="' .
                get_string("view_noTimespan", "block_lemo4moodle") . '" id="viewNoTimespan">
            <input type="hidden" value="' .
                get_string("view_modalError", "block_lemo4moodle") . '" id="viewModalError">
            <!-- Language used by the user -->
            <input type="hidden" value' . $USER->lang . ' id="userLanguage">
        </div>
        </div>

          <!-- JQuery and JQuery Datepicker. -->
          <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/i18n/jquery-ui-i18n.min.js"></script>

          <!-- Google Charts. -->
        <script src="https://www.gstatic.com/charts/loader.js"></script>

          <!-- Materialize CSS Framework - minified - JavaScript. -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>

        <!-- Plotly, Heatmap. -->
        <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>';

    // JS part.
    $content .=
    '<script>

    <!-- Data-variables from lemo_dq_queries.php made usable for the js-files. -->
    var barchartData = '.$barchartdata.';

    var linechartData = '.$linechartdata.';
    var heatmapData = '.$heatmapdata.';

    var firstdate = "'.$firstdate.'";
    var lastdate = "'.$lastdate.'";
    </script>

    <script>'.file_get_contents('js/lemo_barchart.js').'</script>
    <script>'.file_get_contents('js/lemo_linechart.js').'</script>
    <script>'.file_get_contents('js/lemo_heatmap.js').'</script>

    <!-- General functions of the plugin. Must be included after the JS-files of the charts. -->
    <script>'.file_get_contents('js/lemo_view.js').'</script>
    </body>
    </html>';

} else if ($_POST['allCharts'] == 'false') { // If only one chart should be downloaded.
    // HTML part.
    $content =
        '<!DOCTYPE html>
        <html lang="'.get_string('lang', 'block_lemo4moodle').'">

        <head>
            <meta charset="utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>'.get_string('pluginname', 'block_lemo4moodle').'</title>

            <!-- Datepicker jQuery.-->
            <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

            <!-- Materialize CSS Framework - minified CSS. -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">

            <!-- Google Icons. -->
            <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

            <!-- styles.css. -->
            <style>'.file_get_contents('styles.css').'</style>

        </head>
        <body>
            <div class="block_lemo4moodle">
            <!-- Header. -->
            <div class="container-fluid">
                <nav>
                    <div class="nav-wrapper">
                        <a onClick="window.location.reload()" class="brand-logo">
                        <i class="material-icons medium">insert_chart</i>'.get_string('pluginname', 'block_lemo4moodle').'</a>
                    </div>
                </nav>
              <!-- Tabs. -->
            <div class="row">
                <div class="col s12">
                    <ul class="tabs" id="tabs">
                        <li class="tab disabled">
                            <a href="#">'.get_string('logdata', 'block_lemo4moodle').$firstdate.' - '.$lastdate.'</a>
                        </li>
                        <li class="tab" id="tab_barChart">
                            <a class="active" href="#chart1" >'.$_POST['chart'].'</a>
                        </li>
                    </ul>
                </div>
                <div id="chart1" class="col s12">
                    <div class="row">
                        <div class="col s9 chart">
                            <div id="'.$_POST['chart'].'" class="block_lemo4moodle-chart"></div>
                        </div>
                        <div id="options" class="col s3">
                            <div class="row">
                                <div class="input-field col s12">';

    if ($_POST['chart'] == 'linechart') {
        $content .=
            '<div class="divider"></div>
            <p>'.get_string('filter', 'block_lemo4moodle').'</p>
            <input placeholder="'.get_string('filterStart', 'block_lemo4moodle').'"
                type="text" class="datepick " id="datepicker_3">
            <input placeholder="'.get_string('filterEnd', 'block_lemo4moodle').'"
                type="text" class="datepick " id="datepicker_4">
            <button class="btn waves-effect waves-light grey darken-3 button"
                type="submit" name="action" id="dp_button_2">'.
                get_string('update', 'block_lemo4moodle').'</button>
            <button class="btn waves-effect waves-light grey darken-3 button"
                type="submit" name="action" id="rst_btn_2">'.
                get_string('reset', 'block_lemo4moodle').'</button>';
    } else if($_POST['chart'] == 'barchart'){
        $content .=
        '<p>'.get_string('selectStart', 'block_lemo4moodle').'</p>
        <select id="barchart_select_module">
            <option value="all" selected>'.get_string('selectAll', 'block_lemo4moodle').'</option>
        </select>
        <div class="divider"><</div>
        <br>
        <p>'.get_string('filter', 'block_lemo4moodle').'</p>
        <input placeholder="Beginn" type="text" class="datepick " id="datepicker_1">
        <input placeholder="Ende" type="text" class="datepick " id="datepicker_2">
        <button class="btn waves-effect waves-light grey darken-3 button"
            type="submit" name="action" id="dp_button_1">'.
            get_string('update', 'block_lemo4moodle').'</button>
        <button class="btn waves-effect waves-light grey darken-3 button"
            type="submit" name="action" id="rst_btn_1">'.
            get_string('reset', 'block_lemo4moodle').'</button>';
    } else if ($_POST['chart'] == 'heatmap') {
        $content .=
            '<div class="divider"></div>
            <p>'.get_string('filter', 'block_lemo4moodle').'</p>
            <input placeholder="'.get_string('filterStart', 'block_lemo4moodle').'"
                type="text" class="datepick " id="datepicker_5">
            <input placeholder="'.get_string('filterEnd', 'block_lemo4moodle').'"
                type="text" class="datepick " id="datepicker_6">
            <button class="btn waves-effect waves-light grey darken-3 button"
                type="submit" name="action" id="dp_button_3">'.
                get_string('update', 'block_lemo4moodle').'</button>
            <button class="btn waves-effect waves-light grey darken-3 button"
                type="submit" name="action" id="rst_btn_3">'.
                get_string('reset', 'block_lemo4moodle').'</button>';
    }
    $content .=
        '</div>
        </div>
        </div>
        </div>
        </div>
        </div>
        <div id="jsvariables">
            <!-- Language-string variables made accessible for JS. -->
            <input type="hidden" value="' .
                get_string("selectAll", "block_lemo4moodle") . '" id="selectAll">
            <!-- Barchart. -->
            <input type="hidden" value="' .
                get_string("barchart_module", "block_lemo4moodle") . '" id="barchartModule">
            <input type="hidden" value="' .
                get_string("barchart_title", "block_lemo4moodle") . '" id="barchartTitle">
            <input type="hidden" value="' .
                get_string("barchart_xlabel", "block_lemo4moodle") . '" id="barchartXLabel">
            <input type="hidden" value="' .
                get_string("barchart_ylabel", "block_lemo4moodle") . '" id="barchartYLabel">
            <!-- Linechart. -->
            <input type="hidden" value="' .
                get_string("linechart_colDate", "block_lemo4moodle") . '" id="linechartColDate">
            <input type="hidden" value="' .
                get_string("linechart_colAccess", "block_lemo4moodle") . '" id="linechartColAccess">
            <input type="hidden" value="' .
                get_string("linechart_colOwnAccess", "block_lemo4moodle") . '" id="linechartColOwnAccess">
            <input type="hidden" value="' .
                get_string("linechart_colUser", "block_lemo4moodle") . '" id="linechartColUser">
            <input type="hidden" value="' .
                get_string("linechart_colMissingData", "block_lemo4moodle") . '" id="linechartColMissingData">
            <input type="hidden" value="' .
                get_string("linechart_title", "block_lemo4moodle") . '" id="linechartTitle">
            <!--Heatmap.  -->
            <input type="hidden" value="' .
                get_string("heatmap_title", "block_lemo4moodle") . '" id="heatmapTitle">
            <input type="hidden" value="' .
                get_string("heatmap_all", "block_lemo4moodle") . '" id="heatmapAll">
            <input type="hidden" value="' .
                get_string("heatmap_own", "block_lemo4moodle") . '" id="heatmapOwn">
            <input type="hidden" value="' .
                get_string("heatmap_overall", "block_lemo4moodle") . '" id="heatmapOverall">
            <input type="hidden" value="' .
                get_string("heatmap_average", "block_lemo4moodle") . '" id="heatmapAverage">
            <input type="hidden" value="' .
                get_string("heatmap_monday", "block_lemo4moodle") . '" id="heatmapMonday">
            <input type="hidden" value="' .
                get_string("heatmap_tuesday", "block_lemo4moodle") . '" id="heatmapTuesday">
            <input type="hidden" value="' .
                get_string("heatmap_wednesday", "block_lemo4moodle") . '" id="heatmapWednesday">
            <input type="hidden" value="' .
                get_string("heatmap_thursday", "block_lemo4moodle") . '" id="heatmapThursday">
            <input type="hidden" value="' .
                get_string("heatmap_friday", "block_lemo4moodle") . '" id="heatmapFriday">
            <input type="hidden" value="' .
                get_string("heatmap_saturday", "block_lemo4moodle") . '" id="heatmapSaturday">
            <input type="hidden" value="' .
                get_string("heatmap_sunday", "block_lemo4moodle") . '" id="heatmapSunday">
            <!-- Treemap. -->
            <input type="hidden" value="' .
                get_string("treemap_title", "block_lemo4moodle") . '" id="treemapTitle">
            <input type="hidden" value="' .
                get_string("treemap_clickCount", "block_lemo4moodle") . '" id="treemapClickCount">
            <!-- View. -->
            <input type="hidden" value="' .
                get_string('view_checkSelection', 'block_lemo4moodle') . '" id="viewCheckSelection">
            <input type="hidden" value="' .
                get_string("view_dialogThis", "block_lemo4moodle") . '" id="viewDialogThis">
            <input type="hidden" value="' .
                get_string("view_dialogAll", "block_lemo4moodle") . '" id="viewDialogAll">
            <input type="hidden" value="' .
                get_string("view_file", "block_lemo4moodle") . '" id="viewFile">
            <input type="hidden" value="' .
                get_string("view_timespan", "block_lemo4moodle") . '" id="viewTimespan">
            <input type="hidden" value="' .
                get_string("view_noTimespan", "block_lemo4moodle") . '" id="viewNoTimespan">
            <input type="hidden" value="' .
                get_string("view_modalError", "block_lemo4moodle") . '" id="viewModalError">
            <!-- Language used by the user -->
            <input type="hidden" value' . $USER->lang . ' id="userLanguage">
        </div>
        </div>';

    // JS part.
    $content .=
        '<!-- JQuery and JQuery Datepicker. -->
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/i18n/jquery-ui-i18n.min.js"></script>

        <!-- Google Charts. -->
        <script src="https://www.gstatic.com/charts/loader.js"></script>

        <!-- Materialize CSS Framework - minified - JavaScript. -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>

        <!-- Plotly, Heatmap. -->
        <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

        <script>';

    if ($_POST['chart'] == 'barchart') {
        $content .= 'var barchartData = '.$barchartdata.';';
    } else if ($_POST['chart'] == 'linechart') {
        $content .= 'var linechartData = '.$linechartdata.';';
    } else if ($_POST['chart'] == 'heatmap') {
        $content .= 'var heatmapData = '.$heatmapdata.';';
    }

    $content .= '</script>
        <!-- Barchart, linechart and heatmap are loaded. Must be included after the data-variables. -->';
    if ($_POST['chart'] == 'barchart') {
        $content .= '<script>'.file_get_contents('js/lemo_barchart.js').'</script>';
    } else if ($_POST['chart'] == 'linechart') {
        $content .= '<script>'.file_get_contents('js/lemo_linechart.js').'</script>';
    } else if ($_POST['chart'] == 'heatmap') {
        $content .= '<script>'.file_get_contents('js/lemo_heatmap.js').'</script>';
    }

    $content .= '
        <script>
        // Timpespan variables.
        var firstdate = "'.$firstdate.'";
        var lastdate = "'.$lastdate.'";
        </script>

        <!-- General functions of the plugin. Must be included before the JS-files of the charts. -->
        <script>'.file_get_contents('js/lemo_view.js').'</script>

        </body>
        </html>';
}






// Set filename and type of file for the download.
header("Content-type: text/html");
// If merged, change filename.
if (!isset($_POST["mergeData"]) || $_POST["mergeData"] == "") {
    header("Content-Disposition: attachment; filename=lemo4moodle_".$todayfilename.".html");
} else {
    header("Content-Disposition: attachment; filename=lemo4moodle_".$todayfilename."_MERGED.html");
}

echo $content;
