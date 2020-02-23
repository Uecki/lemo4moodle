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
 * @copyright  2020 Finn Ueckert
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_login();

// Get courseid, userid and alldata (encoded data-arrays in JSON-format).
$courseid = $_POST["id"];
$userid = $_POST["userid"];
$alldata = json_decode($_POST["data"], true);

// Get today's date.
$today = date("d.m.y");

// Get date for filename.
$todayfilename = date("Y_m_d");

// Get each dataset from the data array.
if (!isset($_POST["mergeData"]) || $_POST["mergeData"] == "") {
    $linechartdataarray = $alldata[0];
} else {
    $linechartdataarray = json_decode($_POST["mergeData"], true);

    /**
     * Custom comparator used for sorting the array with usort() function.
     *
     * @param array $a
     * @param array $b
     * @return strnatcmp String comparisons using a "natural order" algorithm.
     */
    function compare_date($a, $b) {
        return strnatcmp($a[0], $b[0]);
    }

    // Sort alphabetically by name.
    usort($linechartdataarray, 'compare_date');
    // Add empty data for missing days.
    $needle = array("new Date(", ")");
    $length = count($linechartdataarray);
    $replacement = str_replace($needle, '', $linechartdataarray[$length - 1][0]);
    $replacement2 = str_replace($needle, '', $linechartdataarray[0][0]);
    $datepartstart = explode(", ", $replacement2);
    $startdate = $datepartstart[0].'-'.(intval($datepartstart[1]) + 1).'-'.$datepartstart[2];

    $datepartend = explode(", ", $replacement);
    $enddate = $datepartend[0].'-'.(intval($datepartend[1]) + 1).'-'.$datepartend[2];
    $period = new DatePeriod(
        new DateTime($startdate),
        new DateInterval('P1D'),
        new DateTime($enddate)
    );
    $datetimespan = iterator_to_array($period);
    foreach ($datetimespan as $dt) {
        $tempdatepart = explode("-", $dt->format('Y-m-d'));
        $tempdate = $tempdatepart[0].', '.(intval($tempdatepart[1]) - 1).', '.$tempdatepart[2];
        $dateincluded = false;
        foreach ($linechartdataarray as $aa) {
            if (strpos($aa[0], $tempdate) !== false) {
                $dateincluded = true;
                break;
            }
        }
        if ($dateincluded == false) {
            array_push($linechartdataarray, ["new Date($tempdate)", 0, 0, 0]);
        }
    }

    // Sort again alphabetically by name.
    usort($linechartdataarray, 'compare_date');
}
$barchartarray = $alldata[1];
$heatmaparray = $alldata[2];
$treemaparray  = $alldata[3];
$heatmap = $alldata[4]; // Two heatmap datasets, because of filter function.


// Get the first recorded date of the datasets.
preg_match_all('/\d+/', $linechartdataarray[0][0], $matches);
$firstdate = $matches[0][2].'.'.(intval($matches[0][1]) + 1).'.'.$matches[0][0]; // Month needs to be augmented by 1.

// Get the last recorded date of the datasets.
if (!isset($_POST["mergeData"]) || $_POST["mergeData"] == "") {
    $lastdate = date("d.m.Y");
} else {
    preg_match_all('/\d+/', $linechartdataarray[(count($linechartdataarray) - 1)][0], $matches);
    $lastdate = $matches[0][2].'.'.(intval($matches[0][1]) + 1).'.'.$matches[0][0]; // Month needs to be augmented by 1.
}

// Create linechart data.
$linechart = '';
$linechartarray = '';
$f = 0;
$needle = array("new Date(", ")");
$length = count($linechartdataarray);
foreach ($linechartdataarray as $fo) {
    $replacement = str_replace($needle, '', $fo[0]);
    if ($f < $length - 1) {
        $linechart .= "[(".$fo[0]."), ".$fo[1].", ".$fo[2].", ".$fo[3]."],";
        $linechartarray .= "['".$replacement."', ".$fo[1].", ".$fo[2].", ".$fo[3]."],";
    }
    if ($f == $length - 1) {
        $linechart .= "[(".$fo[0]."), ".$fo[1].", ".$fo[2].", ".$fo[3]."]";
        $linechartarray .= "['".$replacement."', ".$fo[1].", ".$fo[2].", ".$fo[3]."]";
    }

    $f++;
}




// Create barchart data.
$j = 1;
$leng = count($barchartarray);
$barchartdata = '[["'. get_string('barchart_xlabel', 'block_lemo4moodle') .'", "'.
    get_string('barchart_ylabel', 'block_lemo4moodle') .'", "'.
    get_string('barchart_users', 'block_lemo4moodle') .'"],';
foreach ($barchartarray as $bar) {
    if ($j < $leng ) {
        $barchartdata .= '["'.$bar[0].'", '.$bar[1].', '.$bar[2].'],';
    }
    if ($j == $leng ) {
        $barchartdata .= '["'.$bar[0].'", '.$bar[1].', '.$bar[2].']]';
    }
    $j++;
}


// Create treemap data.
$i = 1;
$nodetitle; // Variable for node title.
$lengtree = count($treemaparray);
$treemapdata =
    "[['Name', 'Parent', 'Size', 'Color'],
        ['".get_string('treemap_global', 'block_lemo4moodle')."', null, 0, 0],
            ['".get_string('treemap_files', 'block_lemo4moodle')."', '".
                get_string('treemap_global', 'block_lemo4moodle')."', 0, 0],";

foreach ($treemaparray as $tree) {
    // If-clause for node title. (Maybe) To be expanded for forum, chat and assignments.
    if ($tree[1] == 'content') {
        $nodetitle = get_string('treemap_files', 'block_lemo4moodle');
    }
    if ($i < $lengtree ) {
        $treemapdata .= "['".$tree[0]."', '".$tree[1]."', ".$tree[2].", ".$tree[3]."],";
    }
    if ($i == $lengtree ) {
        $treemapdata .= "['".$tree[0]."', '".$tree[1]."', ".$tree[2].", ".$tree[3]."]]";
    }
    $i++;
}


// Create heatmap data.
$heatmapdata = $heatmaparray;



// Filter array.
$linechartdataarrayfilter = json_encode($linechartdataarray, JSON_NUMERIC_CHECK);
$heatmapdatafilter = json_encode($heatmap, JSON_NUMERIC_CHECK);

// Lemo_linechart.js-file needs adaptation to work as download. !Doesn't look good, but is functional.
$linechartstringjs = str_replace(
'var activityData = [];
            linechartDataArrayFilter.forEach(function(item) {
                if (item.timestamp >= startTimestamp && item.timestamp <= endTimestamp) {
                    activityData.push({
                        date: item.date,
                        accesses: item.accesses,
                        ownhits: item.ownhits,
                        users: item.user
                    });
                }
            });',
'var activityData = [];
linechartDataArrayFilter.forEach(function(item) {

    var mydate = item[0];
    mydate=mydate.split(", ");
    mydate[1] = parseInt(mydate[1]) + 1;
    mydate[1] = mydate[1].toString();
    var newdate = mydate[1] + "/" + mydate[2] + "/" + mydate[0];
    var nd = block_lemo4moodle_to_timestamp(newdate);
    if (nd >= startTimestamp && nd <= endTimestamp) {
        activityData.push({
            date: item[0],
            accesses: item[1],
            ownhits: item[2],
            users: item[3]
        });
    }
});',
file_get_contents('js/lemo_linechart.js')
);

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

        <!-- report_styles.css. -->
        <style>'.file_get_contents('styles.css').'</style>

    </head>
    <body>
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
                        <a class="active" id="tab1" href="#chart1">Barchart</a>
                    </li>
                    <li class="tab" id="tab_activityChart">
                        <a id="tab2" href="#chart2">Activity Chart</a>
                    </li>
                    <li class="tab" id="tab_heatMap">
                        <a id="tab3" href="#chart3">Heatmap</a>
                    </li>
                    <li class="tab" id="tab_treeMap">
                        <a id="tab4" href="#chart4">Treemap</a>
                    </li>
                </ul>
            </div>
            <!-- Barchart. -->
            <div id="chart1" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                      <div id="barchart" class="chart"></div>
                    </div>
                    <div id="options" class="col s3">
                        <div class="row">
                              <div class="input-field col s12"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Linechart/activity chart. -->
            <div id="chart2" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <div id="linechart" class="chart"></div>
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
                        <div  id="heatmap" class="chart"></div>
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
            <!-- Treemap. -->
            <div id="chart4" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <div  id="treemap" class="chart"></div>
                    </div>
                        <div id="options" class="col s3">
                            <div class="row">
                                <div class="input-field col s12"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

          <!-- JQuery and JQuery Datepicker. -->
          <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

          <!-- Google Charts. -->
        <script src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://www.google.com/jsapi"></script>

          <!-- Materialize CSS Framework - minified - JavaScript. -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>

          <!-- Highcharts, Heatmap.-->
          <script src="https://code.highcharts.com/highcharts.js"></script>
          <script src="https://code.highcharts.com/modules/heatmap.js"></script>';

      // JS part.
        $content .=
        '<script>

            <!-- Data-variables from lemo_dq_queries.php made usable for the js-files. -->
            var barchartData = '.$barchartdata.';
            var linechartDataArray = ['.$linechart.'];
            var heatmapData = '.$heatmapdata.';
            var treemapData = '.$treemapdata.';

            var linechartDataArrayFilter = ['.$linechartarray.'];
            var heatmapDataFilter = Object.entries('.$heatmapdatafilter.');

        var firstdate = "'.$firstdate.'";
        var lastdate = "'.$lastdate.'";

        // Language-string variables made accessible for JS.
        // Barchart.
        var barchart_title = "' . get_string('barchart_title', 'block_lemo4moodle') . '";
        var barchart_xlabel = "' . get_string('barchart_xlabel', 'block_lemo4moodle') . '";
        var barchart_ylabel = "' . get_string('barchart_ylabel', 'block_lemo4moodle') . '";
        // Linechart.
        var linechart_colDate = "' . get_string('linechart_colDate', 'block_lemo4moodle') . '";
        var linechart_colAccess = "' . get_string('linechart_colAccess', 'block_lemo4moodle') . '";
        var linechart_colOwnAccess = "' . get_string('linechart_colOwnAccess', 'block_lemo4moodle') . '";
        var linechart_colUser = "' . get_string('linechart_colUser', 'block_lemo4moodle') . '";
        var linechart_title = "' . get_string('linechart_title', 'block_lemo4moodle') . '";
        var linechart_checkSelection = "' . get_string('linechart_checkSelection', 'block_lemo4moodle') . '";
        // Heatmap.
        var heatmap_title = "' . get_string('heatmap_title', 'block_lemo4moodle') . '";
        var heatmap_all = "' . get_string('heatmap_all', 'block_lemo4moodle') . '";
        var heatmap_own = "' . get_string('heatmap_own', 'block_lemo4moodle') . '";
        var heatmap_overall = "' . get_string('heatmap_overall', 'block_lemo4moodle') . '";
        var heatmap_average = "' . get_string('heatmap_average', 'block_lemo4moodle') . '";
        var heatmap_monday = "' . get_string('heatmap_monday', 'block_lemo4moodle') . '";
        var heatmap_tuesday = "' . get_string('heatmap_tuesday', 'block_lemo4moodle') . '";
        var heatmap_wednesday = "' . get_string('heatmap_wednesday', 'block_lemo4moodle') . '";
        var heatmap_thursday = "' . get_string('heatmap_thursday', 'block_lemo4moodle') . '";
        var heatmap_friday = "' . get_string('heatmap_friday', 'block_lemo4moodle') . '";
        var heatmap_saturday = "' . get_string('heatmap_saturday', 'block_lemo4moodle') . '";
        var heatmap_sunday = "' . get_string('heatmap_sunday', 'block_lemo4moodle') . '";
        var heatmap_checkSelection = "' . get_string('heatmap_checkSelection', 'block_lemo4moodle') . '";
        // Treemap.
        var treemap_title = "' . get_string('treemap_title', 'block_lemo4moodle') . '";
        var treemap_clickCount = "' . get_string('treemap_clickCount', 'block_lemo4moodle') . '";
        // View.
        var view_dialogThis = "' . get_string('view_dialogThis', 'block_lemo4moodle') . '";
        var view_dialogAll = "' . get_string('view_dialogAll', 'block_lemo4moodle') . '";
        var view_file = "' . get_string('view_file', 'block_lemo4moodle') . '";
        var view_timespan = "' . get_string('view_timespan', 'block_lemo4moodle') . '";
        var view_noTimespan = "' . get_string('view_noTimespan', 'block_lemo4moodle') . '";
        var view_modalError = "' . get_string('view_modalError', 'block_lemo4moodle') . '";
        </script>

        <script>'.file_get_contents('js/lemo_barchart.js').'</script>
        <script>'.$linechartstringjs.'</script>
        <script>'.file_get_contents('js/lemo_heatmap.js').'</script>
        <script>'.file_get_contents('js/lemo_treemap.js').'</script>

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

            <!-- report_styles.css. -->
            <style>'.file_get_contents('styles.css').'</style>

        </head>
        <body>
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
                            <div id="'.$_POST['chart'].'" class="chart"></div>
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
            </div>';

            // JS part.
            $content .=
            '<!-- JQuery and JQuery Datepicker. -->
            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

            <!-- Google Charts. -->
            <script src="https://www.gstatic.com/charts/loader.js"></script>
            <script src="https://www.google.com/jsapi"></script>

            <!-- Materialize CSS Framework - minified - JavaScript. -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>

            <!-- Highcharts, Heatmap.-->
            <script src="https://code.highcharts.com/highcharts.js"></script>
            <script src="https://code.highcharts.com/modules/heatmap.js"></script>
            <script>';
                if ($_POST['chart'] == 'barchart') {
                $content .= 'var barchartData = '.$barchartdata.';';
                } else if ($_POST['chart'] == 'linechart') {
                    $content .= 'var linechartDataArray = ['.$linechart.'];
                    var linechartDataArrayFilter = ['.$linechartarray.'];';
                } else if ($_POST['chart'] == 'heatmap') {
                    $content .= 'var heatmapData = '.$heatmapdata.';
                    var heatmapDataFilter = Object.entries('.$heatmapdatafilter.');';
                } else if ($_POST['chart'] == 'treemap') {
                    $content .= 'var treemapData = '.$treemapdata.';';
                }

            $content .= '</script>
            <!-- Barchart, linechart, heatmap and treemap are loaded. Must be included after the data-variables. -->';
            if ($_POST['chart'] == 'barchart') {
                $content .= '<script>'.file_get_contents('js/lemo_barchart.js').'</script>';
            } else if ($_POST['chart'] == 'linechart') {
                $content .= '<script>'.$linechartstringjs.'</script>';
            } else if ($_POST['chart'] == 'heatmap') {
                $content .= '<script>'.file_get_contents('js/lemo_heatmap.js').'</script>';
            } else if ($_POST['chart'] == 'treemap') {
                $content .= '<script>'.file_get_contents('js/lemo_treemap.js').'</script>';
            }

            $content .= '
            <script>
            // Timpespan variables.
            var firstdate = "'.$firstdate.'";
            var lastdate = "'.$lastdate.'";

            // Language-string variables made accessible for JS
            // Barchart.
            var barchart_title = "' . get_string('barchart_title', 'block_lemo4moodle') . '";
            var barchart_xlabel = "' . get_string('barchart_xlabel', 'block_lemo4moodle') . '";
            var barchart_ylabel = "' . get_string('barchart_ylabel', 'block_lemo4moodle') . '";
            // Linechart.
            var linechart_colDate = "' . get_string('linechart_colDate', 'block_lemo4moodle') . '";
            var linechart_colAccess = "' . get_string('linechart_colAccess', 'block_lemo4moodle') . '";
            var linechart_colOwnAccess = "' . get_string('linechart_colOwnAccess', 'block_lemo4moodle') . '";
            var linechart_colUser = "' . get_string('linechart_colUser', 'block_lemo4moodle') . '";
            var linechart_title = "' . get_string('linechart_title', 'block_lemo4moodle') . '";
            var linechart_checkSelection = "' . get_string('linechart_checkSelection', 'block_lemo4moodle') . '";
            // Heatmap.
            var heatmap_title = "' . get_string('heatmap_title', 'block_lemo4moodle') . '";
            var heatmap_all = "' . get_string('heatmap_all', 'block_lemo4moodle') . '";
            var heatmap_own = "' . get_string('heatmap_own', 'block_lemo4moodle') . '";
            var heatmap_overall = "' . get_string('heatmap_overall', 'block_lemo4moodle') . '";
            var heatmap_average = "' . get_string('heatmap_average', 'block_lemo4moodle') . '";
            var heatmap_monday = "' . get_string('heatmap_monday', 'block_lemo4moodle') . '";
            var heatmap_tuesday = "' . get_string('heatmap_tuesday', 'block_lemo4moodle') . '";
            var heatmap_wednesday = "' . get_string('heatmap_wednesday', 'block_lemo4moodle') . '";
            var heatmap_thursday = "' . get_string('heatmap_thursday', 'block_lemo4moodle') . '";
            var heatmap_friday = "' . get_string('heatmap_friday', 'block_lemo4moodle') . '";
            var heatmap_saturday = "' . get_string('heatmap_saturday', 'block_lemo4moodle') . '";
            var heatmap_sunday = "' . get_string('heatmap_sunday', 'block_lemo4moodle') . '";
            var heatmap_checkSelection = "' . get_string('heatmap_checkSelection', 'block_lemo4moodle') . '";
            // Treemap.
            var treemap_title = "' . get_string('treemap_title', 'block_lemo4moodle') . '";
            var treemap_clickCount = "' . get_string('treemap_clickCount', 'block_lemo4moodle') . '";
            // View
            var view_dialogThis = "' . get_string('view_dialogThis', 'block_lemo4moodle') . '";
            var view_dialogAll = "' . get_string('view_dialogAll', 'block_lemo4moodle') . '";
            var view_file = "' . get_string('view_file', 'block_lemo4moodle') . '";
            var view_timespan = "' . get_string('view_timespan', 'block_lemo4moodle') . '";
            var view_noTimespan = "' . get_string('view_noTimespan', 'block_lemo4moodle') . '";
            var view_modalError = "' . get_string('view_modalError', 'block_lemo4moodle') . '";
            </script>

            <!-- General functions of the plugin. Must be included after the JS-files of the charts. -->
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
