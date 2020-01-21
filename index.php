<?php
/* include once moodle/report/outline/index.php and prevent any display function  */
ob_start();
//moodle_path from config.php
require_once '../../report/outline/index.php';

require_once '../../config.php';

ob_end_clean();
/* defining PHP errorstatement */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

$courseID = $_GET['id'];
$userID = $_GET['user'];
include_once ('lemo_db_queries.php');

/* "Import" global variables from the Moodle Config (config.php) */
global $COURSE;
global $DB;
global $CFG;
?>

<!DOCTYPE html>
<html lang="<?php echo get_string('lang', 'block_lemo4moodle')?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo get_string('pluginname', 'block_lemo4moodle')?></title>

    <!-- Datepicker jQuery-->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Materialize CSS Framework - minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">

    <!-- Google Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- styles.css -->
    <link rel="stylesheet" href="styles.css">

	<!-- report_styles.css -->
	<link rel="stylesheet" href="styles.css">

</head>

<!-- Start (for lemo_create_html.php) -->

<body>
	<!-- Dialog box -->
	<div id = "dialog" title = "<?php echo get_string('dialogTitle', 'block_lemo4moodle')?>"><?php echo get_string('download_dialog', 'block_lemo4moodle')?></div>
	<!-- Header -->
    <div class="container-fluid">
        <nav>
            <div class="nav-wrapper">
                <a onClick="window.location.reload()" class="brand-logo">
                    <i class="material-icons medium">insert_chart</i><?php echo get_string('pluginname', 'block_lemo4moodle')?></a>
                <ul id="nav" class="right hide-on-med-and-down">
                  <li>
                    <!-- Modal Trigger -->
                   <a class="waves-effect waves-light grey darken-3 btn modal-trigger" href="#modal1"><?php echo get_string('modal_title', 'block_lemo4moodle')?></a>

                   <!-- Modal Structure -->
                   <div id="modal1" class="modal modal-fixed-footer">
                     <div class="modal-content">
                       <h4 class="black-text"><?php echo get_string('modal_title', 'block_lemo4moodle')?></h4>
                        <p class="black-text"><?php echo get_string('modal_content', 'block_lemo4moodle')?>
                        </p>
                        <p class="red-text" id="modal_error2"></p>
                        <div id='fileSelection'>
                          <ul id='fileList'>
                            <li>
                              <!--<p class = 'black-text'>
                                <i class="material-icons medium" name='addIcon' id="addIcon">add</i>
                              </p>-->
                              <input type="file" accept=".html" name="fileMerge"id="file_merge" multiple>
                            </li>
                          </ul>
                        </div>
                        <br>
                        <div class="row">
                          <div class="col s6" id="file_merge_filenames"></div>
                          <div class="col s6" id="file_merge_timespan"></div>
                        </div>
                        <button class="btn waves-effect waves-light grey darken-3 button" id="mergeButton"><?php echo get_string('modal_title', 'block_lemo4moodle')?></button>
                     </div>
                   </div>
                  </li>
                    <!--
					<li>
                        <a href="#" class="waves-effect waves-light btn white red-text" id="btn_manual">Hilfe</a>
                    </li>

                    <li>
                        <a onClick="window.close();" class="waves-effect waves-light btn white red-text" id="btn_close">Schließen</a>
                    </li>
					-->
                </ul>
            </div>
        </nav>
		<!-- Tabs -->
        <div class="row">
            <div class="col s12">
                <ul class="tabs" id="tabs">
                    <li class="tab disabled">
                        <a href="#"><?php echo get_string('logdata', 'block_lemo4moodle')?> <?php echo userdate($minlog);?></a>
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
			<!-- Barchart -->
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
                									<a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_1"><?php echo get_string('html_download', 'block_lemo4moodle')?></a>
                									<!-- Variables that are to be posted to lemo_create_html.php.  -->
                									<input type='hidden' value='<?php echo $courseID ?>' name='id'>
                									<input type='hidden' value='<?php echo $userID ?>' name='userid'>
                									<input type='hidden' value='<?php echo $allData ?>' name='data'>
                									<input type='hidden' value='barchart' name='chart'>
                									<input type='hidden' value='' name='allCharts' id="allCharts1">
                                  <!-- for merging files (only for linechart atm). Merge button only uses the elements of the barchart tab. -->
                                  <input type='hidden' value='' name='mergeData' id="mergeData1" >

                								</form>
                                <div class="divider"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<!-- Linechart/activity chart -->
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
                                    <input placeholder="Beginn" type="text" class="datepick " id="datepicker_3">
                                    <input placeholder="Ende" type="text" class="datepick " id="datepicker_4">
                                    <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="dp_button_2"><?php echo get_string('update', 'block_lemo4moodle')?></button>
                                    <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="rst_btn_2"><?php echo get_string('reset', 'block_lemo4moodle')?></button>
                                    <div class="divider"></div>
                                    <p><?php echo get_string('backup', 'block_lemo4moodle')?></p>
									<form action='lemo_create_html.php' method='post' id='download_form_2'>
										<a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_2"><?php echo get_string('html_download', 'block_lemo4moodle')?></a>
										<!-- Variables that are to be posted to lemo_create_html.php.  -->
										<input type='hidden' value='<?php echo $courseID ?>' name='id'>
										<input type='hidden' value='<?php echo $userID ?>' name='userid'>
										<input type='hidden' value='<?php echo $allData ?>' name='data'>
										<input type='hidden' value='linechart' name='chart'>
										<input type='hidden' value='' name='allCharts' id="allCharts2">
								    </form>
                                    <div class="divider"></div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
			<!-- Heatmap -->
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
                                    <input placeholder="Beginn" type="text" class="datepick " id="datepicker_5">
                                    <input placeholder="Ende" type="text" class="datepick " id="datepicker_6">
                                    <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="dp_button_3"><?php echo get_string('update', 'block_lemo4moodle')?></button>
                                    <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="rst_btn_3"><?php echo get_string('reset', 'block_lemo4moodle')?></button>
                                    <div class="divider"></div>
                                    <p><?php echo get_string('backup', 'block_lemo4moodle')?></p>

									<form action='lemo_create_html.php' method='post' id='download_form_3'>
										<a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_3"><?php echo get_string('html_download', 'block_lemo4moodle')?></a>
										<!-- Variables that are to be posted to lemo_create_html.php.  -->
										<input type='hidden' value='<?php echo $courseID ?>' name='id'>
										<input type='hidden' value='<?php echo $userID ?>' name='userid'>
										<input type='hidden' value='<?php echo $allData ?>' name='data'>
										<input type='hidden' value='heatmap' name='chart'>
										<input type='hidden' value='' name='allCharts' id="allCharts3">
								    </form>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
			<!-- Treemap -->
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
										<a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_4"><?php echo get_string('html_download', 'block_lemo4moodle')?></a>
										<!-- Variables that are to be posted to lemo_create_html.php.  -->
										<input type='hidden' value='<?php echo $courseID ?>' name='id'>
										<input type='hidden' value='<?php echo $userID ?>' name='userid'>
										<input type='hidden' value='<?php echo $allData ?>' name='data'>
										<input type='hidden' value='treemap' name='chart'>
										<input type='hidden' value='' name='allCharts' id="allCharts4">
								    </form>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
		</div>

		<div id="report">
			<ul class="collapsible z-depth-0" data-collapsible="accordion">
				<li>
					<div class="collapsible-header">
						<i class="material-icons right">expand_more</i><?php echo get_string('course_activity', 'block_lemo4moodle')?></div>
					<div class="collapsible-body">
						<span>
							<?php
								//footer of the page, general information about the course
								echo $OUTPUT->container(get_string('computedfromlogs', 'admin', userdate($minlog)) , 'loginfo');
								echo html_writer::table($outlinetable);
							?>
						</span>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<!-- JQuery and JQuery Datepicker -->
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<!-- Google Charts -->
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://www.google.com/jsapi"></script>

	<!-- Materialize CSS Framework - minified - JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>

	<!-- Highcharts, Heatmap-->
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/heatmap.js"></script>

	<!-- End (for lemo_create_html.php) -->

	<script>

		//Data-variables from lemo_dq_queries.php made usable for the js-files.
		var barchart_data = <?php echo $bar_chart_data; ?>;
		var linechart_data = [<?php echo $lineChart; ?>];
		var heatmap_data = <?php echo $heatmap_data; ?>;
		var treemap_data = <?php echo $treemap_data; ?>;

		<?php
			//js variables needed for the filter
			$js_activity = json_encode($finalLineChartObject, JSON_NUMERIC_CHECK);
			echo "var js_activity = ". $js_activity . ";\n";
			$js_heatmap = json_encode($heatmap, JSON_NUMERIC_CHECK);
			echo "var js_heatmap = Object.entries(". $js_heatmap . ");\n";
		?>

    //Language-string variables made accessible for JS
      //Barchart
    var barchart_title = <?php echo '"' . get_string('barchart_title', 'block_lemo4moodle') . '"'?>;
    var barchart_xlabel = <?php echo '"' . get_string('barchart_xlabel', 'block_lemo4moodle') . '"'?>;
    var barchart_ylabel = <?php echo '"' . get_string('barchart_ylabel', 'block_lemo4moodle') . '"'?>;
      //Linechart
    var linechart_colDate = <?php echo '"' . get_string('linechart_colDate', 'block_lemo4moodle') . '"'?>;
    var linechart_colAccess = <?php echo '"' . get_string('linechart_colAccess', 'block_lemo4moodle') . '"'?>;
    var linechart_colOwnAccess = <?php echo '"' . get_string('linechart_colOwnAccess', 'block_lemo4moodle') . '"'?>;
    var linechart_colUser = <?php echo '"' . get_string('linechart_colUser', 'block_lemo4moodle') . '"'?>;
    var linechart_title = <?php echo '"' . get_string('linechart_title', 'block_lemo4moodle') . '"'?>;
    var linechart_checkSelection = <?php echo '"' . get_string('linechart_checkSelection', 'block_lemo4moodle') . '"'?>;
      //Heatmap
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
      //Treemap
    var treemap_title = <?php echo '"' . get_string('treemap_title', 'block_lemo4moodle') . '"'?>;
    var treemap_clickCount = <?php echo '"' . get_string('treemap_clickCount', 'block_lemo4moodle') . '"'?>;
      //View
    var view_dialogThis = <?php echo '"' . get_string('view_dialogThis', 'block_lemo4moodle') . '"'?>;
    var view_dialogAll = <?php echo '"' . get_string('view_dialogAll', 'block_lemo4moodle') . '"'?>;
    var view_file = <?php echo '"' . get_string('view_file', 'block_lemo4moodle') . '"'?>;
    var view_timespan = <?php echo '"' . get_string('view_timespan', 'block_lemo4moodle') . '"'?>;
    var view_noTimespan = <?php echo '"' . get_string('view_noTimespan', 'block_lemo4moodle') . '"'?>;
    var view_modalError = <?php echo '"' . get_string('view_modalError', 'block_lemo4moodle') . '"'?>;

	</script>

	<!-- Barchart, linechart, heatmap and treemap are loaded. Must be included after the data-variables.-->
	<script src="js/lemo_barchart.js"></script>
	<script src="js/lemo_linechart.js"></script>
	<script src="js/lemo_heatmap.js"></script>
	<script src="js/lemo_treemap.js"></script>

	<!-- General functions of the plugin. Must be included after the JS-files of the charts. -->
	<script src="js/lemo_view.js"></script>
</body>
</html>
