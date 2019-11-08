<?php
# include config.php
include 'config.php';
#define Moodle Path
#$moodle_path = 'E:/xampp/htdocs/moodle';
/* include once moodle/report/outline/index.php and prevent any display function  */
ob_start();
include_once (moodle_path.'/report/outline/index.php');

include_once (moodle_path.'/config.php');

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
<html lang="de">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lemo4Moodle</title>

    <!-- Datepicker jQuery-->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Materialize CSS Framework - minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">

    <!-- Google Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	
    <!-- lemo4moodle.css -->
    <link rel="stylesheet" href="lemo4moodle.css">

	<!-- report_styles.css -->
	<link rel="stylesheet" href="styles.css">
	
</head>

<body>
    <div class="container-fluid">
        <nav>
            <div class="nav-wrapper">
                <a onClick="window.location.reload()" class="brand-logo">
                    <i class="material-icons medium">insert_chart</i>Lemo4Moodle</a>
                <ul id="nav" class="right hide-on-med-and-down">
                    <!--
					<li>
                        <a href="http://www.hwr-berlin.de/home/" class="waves-effect waves-light btn white red-text" id="btn_hwr" target="_blank">www.hwr-berlin.de</a>
                    </li>
					-->
                    <li>
                        <a href="#" class="waves-effect waves-light btn white red-text" id="btn_manual">Hilfe</a>
                    </li>
                    <li>
                        <a onClick="window.close();" class="waves-effect waves-light btn white red-text" id="btn_close">Schließen</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="row">
            <div class="col s12">
                <ul class="tabs" id="tabs">
                    <li class="tab disabled">
                        <a href="#">Logdaten seit: <?php echo userdate($minlog);?></a>
                    </li>
                    <li class="tab" id="tab_barChart">
                        <a class="active" href="#chart1" >Bar Chart</a>
                    </li>
                    <li class="tab" id="tab_activityChart">
                        <a href="#chart2">Activity Chart</a>
                    </li>
                    <li class="tab" id="tab_heatMap">
                        <a href="#chart3">Heatmap</a>
                    </li>
                    <li class="tab" id="tab_treeMap">
                        <a href="#chart4">Treemap</a>
                    </li>
                </ul>
            </div>
            <div id="chart1" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <div id="bar_chart" class="chart"></div>
                    </div>
                    <div id="options" class="col s3">
                        <div class="row">
                            <div class="input-field col s12">
							<!--
								<div class="divider"></div>
								<p>Filter:</p>
                                    <input placeholder="Beginn" type="text" class="datepick " id="datepicker_1">
                                    <input placeholder="Ende" type="text" class="datepick " id="datepicker_2">
                                    <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="dp_button_1">Aktualisieren</button>
                                    <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="rst_btn_1">R&uuml;ckg&auml;ngig</button>
                                    <div class="divider"></div>
								-->
                                <p>Datensicherung:</p>
								
                                <!-- Button nicht mehr nötig.
								<a href="<?php echo 'saved_datasets/'.$courseID.'_'.$userID.'/data_'.$courseID.'_'.$userID.'.json'; ?>" download="<?php echo 'lemo4moodle_data_'.$courseID.'_'.$userID.'.json'; ?>" class="btn waves-effect waves-light grey darken-3 button">Raw Data (JSON)</a>
								-->
								
                                <form action='lemo_create_html.php' method='post' id='download_form_1'>
									<a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_1">HTML Download</a>
									<!-- Variables that are to be posted to lemo_create_html.php.  -->
									<input type='hidden' value='<?php echo $courseID ?>' name='id'>
									<input type='hidden' value='<?php echo $userID ?>' name='userid'>
									<input type='hidden' value='<?php echo $allData ?>' name='data'>
									
								</form>
								<!--
								<a href="lemo_create_html.php" download="test.html" class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_1">HTML Download</a>
								-->
                                <div class="divider"></div>
                            </div>                               
                        </div>
                    </div>
                </div>
            </div>
            <div id="chart2" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <div id="line_chart" class="chart"></div>
                    </div>
                        <div id="options" class="col s3">
                            <div class="row">
                                <div class="input-field col s12">
                                    <div class="divider"></div>
                                    <p>Filter:</p>
                                    <input placeholder="Beginn" type="text" class="datepick " id="datepicker_3">
                                    <input placeholder="Ende" type="text" class="datepick " id="datepicker_4">
                                    <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="dp_button_2">Aktualisieren</button>
                                    <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="rst_btn_2">R&uuml;ckg&auml;ngig</button>
                                    <div class="divider"></div>
                                    <p>Datensicherung:</p>
									
									<!-- Button nicht mehr nötig.
                                    <a href="<?php echo 'saved_datasets/'.$courseID.'_'.$userID.'/data_'.$courseID.'_'.$userID.'.json'; ?>" download="<?php echo 'lemo4moodle_data_'.$courseID.'_'.$userID.'.json'; ?>" class="btn waves-effect waves-light grey darken-3 button">Raw Data (JSON)</a>
									-->
									
									<form action='lemo_create_html.php' method='post' id='download_form_3'>
										<a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_2">HTML Download</a>
										<!-- Variables that are to be posted to lemo_create_html.php.  -->
										<input type='hidden' value='<?php echo $courseID ?>' name='id'>
										<input type='hidden' value='<?php echo $userID ?>' name='userid'>
										<input type='hidden' value='<?php echo $allData ?>' name='data'>
								    </form>
									<!--
                                    <a href="#" download="test.html" class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_2">Download HTML</a>
									-->
                                    <div class="divider"></div>
                                </div>                               
                            </div>
                        </div>
                </div>
            </div>
            <div id="chart3" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                       <div  id="heatmap" class="chart"></div>
                    </div>
                        <div id="options" class="col s3">
                            <div class="row">
                                <div class="input-field col s12">
									<div class="divider"></div>
									<p>Filter:</p>
                                    <input placeholder="Beginn" type="text" class="datepick " id="datepicker_5">
                                    <input placeholder="Ende" type="text" class="datepick " id="datepicker_6">
                                    <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="dp_button_3">Aktualisieren</button>
                                    <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="rst_btn_3">R&uuml;ckg&auml;ngig</button>
                                    <div class="divider"></div>
                                    <p>Datensicherung:</p>
									
									<form action='lemo_create_html.php' method='post' id='download_form_2'>
										<a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_3">HTML Download</a>
										<!-- Variables that are to be posted to lemo_create_html.php.  -->
										<input type='hidden' value='<?php echo $courseID ?>' name='id'>
										<input type='hidden' value='<?php echo $userID ?>' name='userid'>
										<input type='hidden' value='<?php echo $allData ?>' name='data'>
								    </form>
                                </div>                               
                            </div>
                    </div>
                </div>    
            </div>
            <div id="chart4" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <div  id="treemap" class="chart"></div>
                    </div>
                        <div id="options" class="col s3">
                            <div class="row">
                                <div class="input-field col s12">
                                    <p>Datensicherung:</p>
									
									<form action='lemo_create_html.php' method='post' id='download_form_4'>
										<a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_4">HTML Download</a>
										<!-- Variables that are to be posted to lemo_create_html.php.  -->
										<input type='hidden' value='<?php echo $courseID ?>' name='id'>
										<input type='hidden' value='<?php echo $userID ?>' name='userid'>
										<input type='hidden' value='<?php echo $allData ?>' name='data'>
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
						<i class="material-icons right">expand_more</i>Kursaktivität (Moodle Bericht)</div>
					<div class="collapsible-body">
						<span>
							<?php   
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
	
	<script>
		
		<!-- Data-variables from lemo_dq_queries.php made usable for the js-files. -->
		var barchart_data = <?php echo $bar_chart_data; ?>;
		var linechart_data = [<?php echo $lineChart; ?>];
		var heatmap_data = <?php echo $heatmap_data; ?>;
		var treemap_data = <?php echo $treemap_data; ?>;
		
		<?php
			$js_activity = json_encode($finalLineChartObject, JSON_NUMERIC_CHECK);
			echo "var js_activity = ". $js_activity . ";\n";
			$js_heatmap = json_encode($heatmap, JSON_NUMERIC_CHECK);
			echo "var js_heatmap = Object.entries(". $js_heatmap . ");\n";
		?>
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





