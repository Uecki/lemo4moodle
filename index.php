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
 * @copyright  2020 Finn Ueckert
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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Materialize CSS Framework - minified CSS. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">

    <!-- Google Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- styles.css -->
    <link rel="stylesheet" href="styles.css">

    <!-- report_styles.css -->
    <link rel="stylesheet" href="styles.css">

</head>

<!-- Start (for lemo_create_html.php). -->

<body>
    <!-- Dialog box. -->
    <div id = "dialog" title = "<?php echo get_string('dialogTitle', 'block_lemo4moodle')?>">
        <?php echo get_string('download_dialog', 'block_lemo4moodle')?>
    </div>
    <!-- Header. -->
    <div class="container-fluid">
        <nav>
            <div class="nav-wrapper">
                <a onClick="window.location.reload()" class="brand-logo">
                    <i class="material-icons medium">insert_chart</i>
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
                                    <!-- For merging files (only for linechart atm).
                                    Merge button only uses the elements of the barchart tab. -->
                                    <input type='hidden' value='' name='mergeData' id="mergeData1" >
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
                        <div id="linechart" class="chart"></div>
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
                       <div  id="heatmap" class="chart"></div>
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
            <!-- Treemap. -->
            <div id="chart4" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <div  id="treemap" class="chart"></div>
                    </div>
                    <div id="options" class="col s3">
                        <div class="row">
                            <div class="input-field col s12">
                                <p><?php echo get_string('backup', 'block_lemo4moodle')?></p>
                                <form action='lemo_create_html.php' method='post' id='download_form_4'>
                                    <a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_4">
                                        <?php echo get_string('html_download', 'block_lemo4moodle')?>
                                    </a>
                                    <!-- Variables that are to be posted to lemo_create_html.php.  -->
                                    <input type='hidden' value='<?php echo $courseid ?>' name='id'>
                                    <input type='hidden' value='<?php echo $userid ?>' name='userid'>
                                    <input type='hidden' value='<?php echo $alldata ?>' name='data'>
                                    <input type='hidden' value='treemap' name='chart'>
                                    <input type='hidden' value='' name='allCharts' id="allCharts4">
                                </form>
                            </div>
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

    <!-- Highcharts, Heatmap. -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/heatmap.js"></script>

    <!-- End (for lemo_create_html.php). -->

    <script>

        // Data-variables from lemo_dq_queries.php made usable for the js-files.
        var barchartdata = <?php echo $barchartdata; ?>;
        var linechartdataarray = [<?php echo $linechart; ?>];
        var heatmapdata = <?php echo $heatmapdata; ?>;
        var treemapdata = <?php echo $treemapdata; ?>;

        <?php
        // JS variables needed for the filter.
        $linechartdataarrayfilter = json_encode($finallinechartobject, JSON_NUMERIC_CHECK);
        echo "var linechartdataarrayfilter = ". $linechartdataarrayfilter . ";\n";
        $heatmapdatafilter = json_encode($heatmap, JSON_NUMERIC_CHECK);
        echo "var heatmapdatafilter = Object.entries(". $heatmapdatafilter . ");\n";
        ?>

        // Language-string variables made accessible for JS.
        // Barchart.
        var barchart_title = <?php echo '"' . get_string('barchart_title', 'block_lemo4moodle') . '"'?>;
        var barchart_xlabel = <?php echo '"' . get_string('barchart_xlabel', 'block_lemo4moodle') . '"'?>;
        var barchart_ylabel = <?php echo '"' . get_string('barchart_ylabel', 'block_lemo4moodle') . '"'?>;
        // Linechart.
        var linechart_colDate = <?php echo '"' . get_string('linechart_colDate', 'block_lemo4moodle') . '"'?>;
        var linechart_colAccess = <?php echo '"' . get_string('linechart_colAccess', 'block_lemo4moodle') . '"'?>;
        var linechart_colOwnAccess = <?php echo '"' . get_string('linechart_colOwnAccess', 'block_lemo4moodle') . '"'?>;
        var linechart_colUser = <?php echo '"' . get_string('linechart_colUser', 'block_lemo4moodle') . '"'?>;
        var linechart_title = <?php echo '"' . get_string('linechart_title', 'block_lemo4moodle') . '"'?>;
        var linechart_checkSelection = <?php echo '"' . get_string('linechart_checkSelection', 'block_lemo4moodle') . '"'?>;
        // Heatmap.
        var heatmap_title = <?php echo '"' . get_string('heatmap_title', 'block_lemo4moodle') . '"'?>;
        var heatmap_all = <?php echo '"' . get_string('heatmap_all', 'block_lemo4moodle') . '"'?>;
        var heatmap_own = <?php echo '"' . get_string('heatmap_own', 'block_lemo4moodle') . '"'?>;
        var heatmap_overall = <?php echo '"' . get_string('heatmap_overall', 'block_lemo4moodle') . '"'?>;
        var heatmap_average = <?php echo '"' . get_string('heatmap_average', 'block_lemo4moodle') . '"'?>;
        var heatmap_monday = <?php echo '"' . get_string('heatmap_monday', 'block_lemo4moodle') . '"'?>;
        var heatmap_tuesday = <?php echo '"' . get_string('heatmap_tuesday', 'block_lemo4moodle') . '"'?>;
        var heatmap_wednesday = <?php echo '"' . get_string('heatmap_wednesday', 'block_lemo4moodle') . '"'?>;
        var heatmap_thursday = <?php echo '"' . get_string('heatmap_thursday', 'block_lemo4moodle') . '"'?>;
        var heatmap_friday = <?php echo '"' . get_string('heatmap_friday', 'block_lemo4moodle') . '"'?>;
        var heatmap_saturday = <?php echo '"' . get_string('heatmap_saturday', 'block_lemo4moodle') . '"'?>;
        var heatmap_sunday = <?php echo '"' . get_string('heatmap_sunday', 'block_lemo4moodle') . '"'?>;
        var heatmap_checkSelection = <?php echo '"' . get_string('heatmap_checkSelection', 'block_lemo4moodle') . '"'?>;
        // Treemap.
        var treemap_title = <?php echo '"' . get_string('treemap_title', 'block_lemo4moodle') . '"'?>;
        var treemap_clickCount = <?php echo '"' . get_string('treemap_clickCount', 'block_lemo4moodle') . '"'?>;
        // View.
        var view_dialogThis = <?php echo '"' . get_string('view_dialogThis', 'block_lemo4moodle') . '"'?>;
        var view_dialogAll = <?php echo '"' . get_string('view_dialogAll', 'block_lemo4moodle') . '"'?>;
        var view_file = <?php echo '"' . get_string('view_file', 'block_lemo4moodle') . '"'?>;
        var view_timespan = <?php echo '"' . get_string('view_timespan', 'block_lemo4moodle') . '"'?>;
        var view_noTimespan = <?php echo '"' . get_string('view_noTimespan', 'block_lemo4moodle') . '"'?>;
        var view_modalError = <?php echo '"' . get_string('view_modalError', 'block_lemo4moodle') . '"'?>;

    </script>

    <!-- Barchart, linechart, heatmap and treemap are loaded. Must be included after the data-variables. -->
    <script src="js/lemo_barchart.js"></script>
    <script src="js/lemo_linechart.js"></script>
    <script src="js/lemo_heatmap.js"></script>
    <script src="js/lemo_treemap.js"></script>

    <!-- General functions of the plugin. Must be included after the JS-files of the charts. -->
    <script src="js/lemo_view.js"></script>
</body>
</html>
<?php
