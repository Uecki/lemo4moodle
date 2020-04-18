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
 * This file is the main file of the block and inlcudes all HTML elements.
 *
 * In this file, the HTML structure is built.
 * Furthermore, it includes the data from moodle database (lemo_db_queries.php),
 * and uses script from the "JS" dircetory. These files are outsourced for a better
 * understanding of the code. There is one for each chart and another one for
 * general functionality.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Finn Ueckert, Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_login();

// Defining PHP errorstatement.
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

$courseid = $_GET['id'];
$userid = $_GET['user'];
require_once(__DIR__.'/lemo_db_queries.php');

?>

<!DOCTYPE html>
<html lang="<?php echo get_string('lang', 'block_lemo4moodle')?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo get_string('pluginname', 'block_lemo4moodle')?></title>

    <!-- Datepicker jQuery-->
    <link rel="stylesheet" href="lib/jquery/jquery-ui.css">
    <link rel="stylesheet" href="lib/jquery/jquery-ui.theme.min.css">

    <!-- Materialize CSS Framework - minified CSS. -->
    <link rel="stylesheet" href="lib/materialize/css/materialize.min.css">

    <!-- Google Icons -->
    <link rel="stylesheet" href="lib/materialicons/icon.css">

    <!-- styles.css -->
    <link rel="stylesheet" href="styles.css">

</head>

<!-- Start (for lemo_create_html.php). -->

<body>
    <div class="block_lemo4moodle">
        <!-- Header. -->
        <div class="container-fluid">
            <nav>
                <div class="nav-wrapper">
                    <a onClick="window.location.reload()" class="brand-logo">
                        <i class="material-icons">insert_chart</i>
                        <?php echo get_string('pluginname', 'block_lemo4moodle')?>
                    </a>
                    <ul id="nav" class="right hide-on-med-and-down">
                        <li>
                            <!-- Modal Trigger. -->
                            <a class="waves-effect waves-light grey darken-3 btn modal-trigger" href="#modal1">
                                <?php echo get_string('modal_title', 'block_lemo4moodle')?>
                            </a>

                            <!-- Modal Structure. -->
                            <div id="modal1" class="modal modal-fixed-footer">
                                <div class="modal-content">
                                    <h4 class="black-text"><?php echo get_string('modal_title', 'block_lemo4moodle')?></h4>
                                    <p class="black-text"><?php echo get_string('modal_content', 'block_lemo4moodle')?></p>
                                    <p class="red-text" id="modal_error2"></p>
                                    <div id='fileSelection'>
                                        <ul id='fileList'>
                                            <li>
                                                <input type="file" accept=".html" name="filemerge" id="file_merge" multiple>
                                            </li>
                                        </ul>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col s6" id="file_merge_filenames"></div>
                                        <div class="col s6" id="file_merge_timespan"></div>
                                    </div>
                                    <button class="btn waves-effect waves-light grey darken-3 button" id="mergeButton">
                                        <?php echo get_string('modal_title', 'block_lemo4moodle')?>
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- Tabs. -->
            <div class="row">
                <div class="col s12">
                    <ul class="tabs" id="tabs">
                        <li class="tab disabled">
                            <a href="#">
                                <?php echo get_string('logdata', 'block_lemo4moodle')?>
                                <?php echo $firstdateindex;?>
                            </a>
                        </li>
                        <li class="tab" id="tab_barChart">
                            <a class="active" id="tab1" href="#chart1">Barchart</a>
                        </li>
                        <li class="tab" id="tab_activityChart">
                            <a id="tab2" href="#chart2">Linechart</a> <!-- Previously activity chart. -->
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
                                    <p><?php echo get_string('backup', 'block_lemo4moodle')?></p>
                                    <form action='lemo_create_html.php' method='post' id='download_form_1'>
                                        <a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_1">
                                            <?php echo get_string('html_download', 'block_lemo4moodle')?>
                                        </a>
                                        <!-- Variables that are to be posted to lemo_create_html.php.  -->
                                        <input type='hidden' value='<?php echo $courseid ?>' name='id'>
                                        <input type='hidden' value='<?php echo $userid ?>' name='userid'>
                                        <input type='hidden' value='<?php echo $alldata ?>' name='data'>
                                        <input type='hidden' value='barchart' name='chart'>
                                        <input type='hidden' value='' name='allCharts' id="allCharts1">
                                    </form>
                                    <div class="divider"></div>
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
                                    <p><?php echo get_string('filter', 'block_lemo4moodle')?></p>
                                    <input placeholder="<?php echo get_string('filterStart', 'block_lemo4moodle')?>"
                                        type="text" class="datepick " id="datepicker_3">
                                    <input placeholder="<?php echo get_string('filterEnd', 'block_lemo4moodle')?>"
                                        type="text" class="datepick " id="datepicker_4">
                                    <button class="btn waves-effect waves-light grey darken-3 button"
                                            type="submit" name="action" id="dp_button_2">
                                        <?php echo get_string('update', 'block_lemo4moodle')?>
                                    </button>
                                    <button class="btn waves-effect waves-light grey darken-3 button"
                                            type="submit" name="action" id="rst_btn_2">
                                        <?php echo get_string('reset', 'block_lemo4moodle')?>
                                    </button>
                                    <div class="divider"></div>
                                    <p><?php echo get_string('backup', 'block_lemo4moodle')?></p>
                                    <form action='lemo_create_html.php' method='post' id='download_form_2'>
                                        <a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_2">
                                            <?php echo get_string('html_download', 'block_lemo4moodle')?>
                                        </a>
                                        <!-- Variables that are to be posted to lemo_create_html.php.  -->
                                        <input type='hidden' value='<?php echo $courseid ?>' name='id'>
                                        <input type='hidden' value='<?php echo $userid ?>' name='userid'>
                                        <input type='hidden' value='<?php echo $alldata ?>' name='data'>
                                        <input type='hidden' value='linechart' name='chart'>
                                        <input type='hidden' value='' name='allCharts' id="allCharts2">
                                        <!-- For merging files (only for linechart atm). -->
                                        <input type='hidden' value='' name='mergeData' id="mergeData2" >
                                    </form>
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
                                    <p><?php echo get_string('filter', 'block_lemo4moodle')?></p>
                                    <input placeholder="<?php echo get_string('filterStart', 'block_lemo4moodle')?>"
                                        type="text" class="datepick " id="datepicker_5">
                                    <input placeholder="<?php echo get_string('filterEnd', 'block_lemo4moodle')?>"
                                        type="text" class="datepick " id="datepicker_6">
                                    <button class="btn waves-effect waves-light grey darken-3 button"
                                            type="submit" name="action" id="dp_button_3">
                                        <?php echo get_string('update', 'block_lemo4moodle')?>
                                    </button>
                                    <button class="btn waves-effect waves-light grey darken-3 button"
                                            type="submit" name="action" id="rst_btn_3">
                                        <?php echo get_string('reset', 'block_lemo4moodle')?>
                                    </button>
                                    <div class="divider"></div>
                                    <p><?php echo get_string('backup', 'block_lemo4moodle')?></p>
                                    <form action='lemo_create_html.php' method='post' id='download_form_3'>
                                        <a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_3">
                                            <?php echo get_string('html_download', 'block_lemo4moodle')?>
                                        </a>
                                        <!-- Variables that are to be posted to lemo_create_html.php.  -->
                                        <input type='hidden' value='<?php echo $courseid ?>' name='id'>
                                        <input type='hidden' value='<?php echo $userid ?>' name='userid'>
                                        <input type='hidden' value='<?php echo $alldata ?>' name='data'>
                                        <input type='hidden' value='heatmap' name='chart'>
                                        <input type='hidden' value='' name='allCharts' id="allCharts3">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dialog box. -->
        <div id = "dialog" title = "<?php echo get_string('dialogTitle', 'block_lemo4moodle')?>">
            <?php echo get_string('download_dialog', 'block_lemo4moodle')?>
        </div>
        <div id="jsvariables">
            <input type='hidden' value=
                '<?php echo json_encode($barchartfileinfo); ?>' id='barchartFileInfo'>
            <input type='hidden' value=
                '<?php echo $CFG->wwwroot; ?>' id="wwwroot">
            <!-- Barchart. -->
            <input type='hidden' value=
                '<?php echo get_string('barchart_title', 'block_lemo4moodle')?>' id="barchartTitle">
            <input type='hidden' value=
                '<?php echo get_string('barchart_xlabel', 'block_lemo4moodle')?>' id="barchartXLabel">
            <input type='hidden' value=
                '<?php echo get_string('barchart_ylabel', 'block_lemo4moodle')?>' id="barchartYLabel">
            <!-- Linechart. -->
            <input type='hidden' value=
                '<?php echo get_string('linechart_colDate', 'block_lemo4moodle')?>' id="linechartColDate">
            <input type='hidden' value=
                '<?php echo get_string('linechart_colAccess', 'block_lemo4moodle')?>' id="linechartColAccess">
            <input type='hidden' value=
                '<?php echo get_string('linechart_colOwnAccess', 'block_lemo4moodle')?>' id="linechartColOwnAccess">
            <input type='hidden' value=
                '<?php echo get_string('linechart_colUser', 'block_lemo4moodle')?>' id="linechartColUser">
            <input type='hidden' value=
                '<?php echo get_string('linechart_title', 'block_lemo4moodle')?>' id="linechartTitle">
            <input type='hidden' value=
                '<?php echo get_string('linechart_checkSelection', 'block_lemo4moodle')?>' id="linechartCheckSelection">
            <!--Heatmap.  -->
            <input type='hidden' value=
                '<?php echo get_string('heatmap_title', 'block_lemo4moodle')?>' id="heatmapTitle">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_all', 'block_lemo4moodle')?>' id="heatmapAll">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_own', 'block_lemo4moodle')?>' id="heatmapOwn">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_overall', 'block_lemo4moodle')?>' id="heatmapOverall">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_average', 'block_lemo4moodle')?>' id="heatmapAverage">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_monday', 'block_lemo4moodle')?>' id="heatmapMonday">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_tuesday', 'block_lemo4moodle')?>' id="heatmapTuesday">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_wednesday', 'block_lemo4moodle')?>' id="heatmapWednesday">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_thursday', 'block_lemo4moodle')?>' id="heatmapThursday">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_friday', 'block_lemo4moodle')?>' id="heatmapFriday">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_saturday', 'block_lemo4moodle')?>' id="heatmapSaturday">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_sunday', 'block_lemo4moodle')?>' id="heatmapSunday">
            <input type='hidden' value=
                '<?php echo get_string('heatmap_checkSelection', 'block_lemo4moodle')?>' id="heatmapCheckSelection">
            <!-- Treemap. -->
            <input type='hidden' value=
                '<?php echo get_string('treemap_title', 'block_lemo4moodle')?>' id="treemapTitle">
            <input type='hidden' value=
                '<?php echo get_string('treemap_clickCount', 'block_lemo4moodle')?>' id="treemapClickCount">
            <!-- View. -->
            <input type='hidden' value=
                '<?php echo get_string('view_dialogThis', 'block_lemo4moodle')?>' id="viewDialogThis">
            <input type='hidden' value=
                '<?php echo get_string('view_dialogAll', 'block_lemo4moodle')?>' id="viewDialogAll">
            <input type='hidden' value=
                '<?php echo get_string('view_file', 'block_lemo4moodle')?>' id="viewFile">
            <input type='hidden' value=
                '<?php echo get_string('view_timespan', 'block_lemo4moodle')?>' id="viewTimespan">
            <input type='hidden' value=
                '<?php echo get_string('view_noTimespan', 'block_lemo4moodle')?>' id="viewNoTimespan">
            <input type='hidden' value=
                '<?php echo get_string('view_modalError', 'block_lemo4moodle')?>' id="viewModalError">
        </div>
    </div>

    <script>
        // Data-variables from lemo_dq_queries.php made usable for the js-files.
        var barchartData = <?php echo $barchartdata; ?>;
        var linechartDataArray = [<?php echo $linechart; ?>];
        var heatmapData = <?php echo $heatmapdata; ?>;
        var treemapData = <?php echo $treemapdata; ?>;
        <?php
        // JS variables needed for the filter.
        $linechartdataarrayfilter = json_encode($finallinechartobject, JSON_NUMERIC_CHECK);
        echo "var linechartDataArrayFilter = ". $linechartdataarrayfilter . ";\n";
        $heatmapdatafilter = json_encode($heatmap, JSON_NUMERIC_CHECK);
        echo "var heatmapDataFilter = Object.entries(". $heatmapdatafilter . ");\n";
        ?>
    </script>

    <!-- JQuery and JQuery Datepicker. -->
    <script src="lib/jquery/jquery.js"></script>
    <script src="lib/jquery/jquery-ui.js"></script>

    <!-- Materialize CSS Framework - minified - JavaScript. -->
    <script src="lib/materialize/js/materialize.min.js"></script>

    <!-- Plotly, Heatmap. -->
    <script src="lib/plotly/plotly-latest.min.js"></script>

    <!-- End (for lemo_create_html.php). -->

    <!-- Barchart, linechart, heatmap and treemap are loaded. Must be included after the data-variables. -->
    <script src="js/lemo_barchart.js"></script>
    <script src="js/lemo_linechart.js"></script>
    <script src="js/lemo_heatmap.js"></script>
    <!--<script src="js/lemo_treemap.js"></script>-->

    <!-- General functions of the plugin. Must be included after the JS-files of the charts. -->
    <script src="js/lemo_view.js"></script>
</body>
</html>
<?php
