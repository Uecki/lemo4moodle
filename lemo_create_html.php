<?php
/*lemo_create_html.php creates a html file containing the recent data and provides a download link for it*/


// include once moodle/report/outline/index.php and prevent any display function
ob_start();
require_once '../../report/outline/index.php';
require_once '../../config.php';
ob_end_clean();

//get courseID, userID and allData (encoded data-arrays in JSON-format)
$courseID = $_POST["id"];
$userID = $_POST["userid"];
$allData = json_decode($_POST["data"], true);

// get today's date
$heute = date("d.m.y");

// get date for Filename
$heute_filename = date("Y_m_d");

// set path for html file (to save it and to provide a download link)
//$pathForHTML = "saved_datasets/".$courseID."_".$userID."/data_".$courseID."_".$userID.".html";

// get data from moodle/report/activity_report (access possible through /report/outline/index.php)
$table = html_writer::table($outlinetable);

// get each dataset from the data array
if(!isset($_POST["mergeData"]) || $_POST["mergeData"] == "") {
  $activity_array = $allData[0];
}
else{
  $activity_array = JSON_decode($_POST["mergeData"], true);
  //$activity_array_test = $_POST["mergeData"];
  //var_dump($activity_array);
  function compare_date($a, $b){
    return strnatcmp($a[0], $b[0]);
  }

  // sort alphabetically by name
  usort($activity_array, 'compare_date');
  //var_dump($activity_array);
}
$barchart_array = $allData[1];
$heatmap_array = $allData[2];
$treemap_array  = $allData[3];
$heatmap = $allData[4]; #two heatmap datasets, because of filter function




// create lineChart data
$lineChart = '';
$lineChartArray = '';
$test2 = '';
$f = 0;
$needle = array("new Date(", ")");
$length = count($activity_array);
foreach($activity_array as $fO){
    $replacement = str_replace($needle, '', $fO[0]);
    if ($f < $length - 1){
        $lineChart .= "[(".$fO[0]."), ".$fO[1].", ".$fO[2].", ".$fO[3]."],";
        $lineChartArray .= "['".$replacement."', ".$fO[1].", ".$fO[2].", ".$fO[3]."],";
    }
    if($f == $length -1){
        $lineChart .= "[(".$fO[0]."), ".$fO[1].", ".$fO[2].", ".$fO[3]."]";
        $lineChartArray .= "['".$replacement."', ".$fO[1].", ".$fO[2].", ".$fO[3]."]";
    }

    $f++;
}




//create bar chart data
$j = 1;
$leng = count($barchart_array);
$barchart_data_array = array();
$bar_chart_data = '[["Dateiname", "Zugriffe", "Nutzer"],';
foreach($barchart_array as $bar){
    if ($j < $leng ){
        $bar_chart_data .= '["'.$bar[0].'", '.$bar[1].', '.$bar[2].'],';
    }
    if($j == $leng ){
        $bar_chart_data .= '["'.$bar[0].'", '.$bar[1].', '.$bar[2].']]';
    }
    $j++;
}


//create treemap data
$i = 1;
$nodeTitle; #variable for node title
$lengTree = count($treemap_array);
$treemap_data_array = array();
$treemap_data =
	"[['Name', 'Parent', 'Size', 'Color'],
		['Global', null, 0, 0],
			['Dateien', 'Global', 0, 0],";

foreach($treemap_array as $tree){
	#if-clause for node title. (Maybe) To be expanded for forum, chat and assignments.
	if ($tree[1] == 'content') {
		$nodeTitle = 'Dateien';
	}
	#else if ()...

	if ($i < $lengTree ){
		$treemap_data .= "['".$tree[0]."', '".$tree[1]."', ".$tree[2].", ".$tree[3]."],";
	}
	if($i == $lengTree ){
		$treemap_data .= "['".$tree[0]."', '".$tree[1]."', ".$tree[2].", ".$tree[3]."]]";
	}
	$i++;
}


//create heatmap data
$heatmap_data = $heatmap_array;



// filter array
$js_activity = json_encode($activity_array, JSON_NUMERIC_CHECK);
$js_heatmap = json_encode($heatmap, JSON_NUMERIC_CHECK);

//lemo_linechart.js-file needs adaptation to work as download. !Doesn't look good, but is functional.
$linechart_js_string = str_replace(
'var activity_data = [];
			js_activity.forEach(function(item) {
				if (item.timestamp >= tp_start && item.timestamp <= tp_end) {
					activity_data.push({
						date: item.datum,
						accesses: item.zugriffe,
						ownhits: item.ownHits,
						users: item.nutzer
					});
				}
			});',
'var activity_data = [];
js_activity.forEach(function (item) {

	var myDate=item[0];
	myDate=myDate.split(", ");
	//console.log(myDate);
	//console.log(myDate[1]);
	myDate[1]=parseInt(myDate[1])+1;
	console.log(myDate[1]);
	myDate[1]=myDate[1].toString();
	var newDate=myDate[1]+"/"+myDate[2]+"/"+myDate[0];
	var nd = toTimestamp(newDate);
	//console.log(nd);
	if (nd >= tp_start && nd <= tp_end) {
		activity_data.push({
			date: item[0],
			accesses: item[1],
			ownhits: item[2],
			users: item[3]
		});
	}
});',
file_get_contents('js/lemo_linechart.js')
);

//initializing content variable
$content = "";

// setting the content, depending on if the users wants all charts or only one
if ($_POST['allCharts'] == 'true') {
	/* For later implementation --> remove need to include changes twice, in index.php and in this file
	$indexString = file_get_contents('index.php');
	$trimmedIndexString = substr($indexString, strrpos($indexString, '<!-- Start'), (strrpos($indexString, '<!-- End')-strrpos($indexString, '<!-- Start')));
	*/
	$content =
	'<!DOCTYPE html>
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

		<!-- styles.css -->
		<style>'.file_get_contents('styles.css').'</style>

		<!-- report_styles.css -->
		<style>'.file_get_contents('styles.css').'</style>

	</head>';

	$content .=
	'
<body>
    <div class="container-fluid">
        <nav>
            <div class="nav-wrapper">
                <a onClick="window.location.reload()" class="brand-logo">
                    <i class="material-icons medium">insert_chart</i>Lemo4Moodle</a>
                <ul id="nav" class="right hide-on-med-and-down">
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
        <div class="row">
            <div class="col s12">
                <ul class="tabs" id="tabs">
                    <li class="tab disabled">
                        <a href="#">Lokale Version erstellt: '.$heute.'</a>
                    </li>
                    <li class="tab" id="tab_barChart">
                        <a class="active" id="tab1" href="#chart1" >Barchart</a>
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
            <div id="chart1" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <div id="barchart" class="chart"></div>
                    </div>
                    <div id="options" class="col s3">
                        <div class="row">
                            <div class="input-field col s12">
                                <div class="divider"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="chart2" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <div id="linechart" class="chart"></div>
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
							'.$table.'
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
	<script src="https://code.highcharts.com/modules/heatmap.js"></script>';

	$content .=
	'<script>'.file_get_contents('js/lemo_barchart.js').'</script>
	<script>'.$linechart_js_string.'</script>
	<script>'.file_get_contents('js/lemo_heatmap.js').'</script>
	<script>'.file_get_contents('js/lemo_treemap.js').'</script>
	<script>

		<!-- Data-variables from lemo_dq_queries.php made usable for the js-files. -->
		var barchart_data = '.$bar_chart_data.';
		var linechart_data = ['.$lineChart.'];
		var heatmap_data = '.$heatmap_data.';
		var treemap_data = '.$treemap_data.';

		var js_activity = ['.$lineChartArray.'];
		var js_heatmap = Object.entries('.$js_heatmap.');
	</script>
	<!-- General functions of the plugin. Must be included after the JS-files of the charts. -->
		<script>'.file_get_contents('js/lemo_view.js').'</script>
	</body>
	</html>';
}

//if only one chart should be downloaded
else if ($_POST['allCharts'] == 'false') {
	$content =
	'<!DOCTYPE html>
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

		<!-- styles.css -->
		<style>'.file_get_contents('styles.css').'</style>

		<!-- report_styles.css -->
		<style>'.file_get_contents('styles.css').'</style>

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
			<div class="row">
				<div class="col s12">
					<ul class="tabs" id="tabs">
						<li class="tab" id="tab_chart">
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
									if($_POST['chart'] == 'linechart') {
										$content .=
										'<div class="divider"></div>
											<p>Filter:</p>
											<input placeholder="Beginn" type="text" class="datepick " id="datepicker_3">
											<input placeholder="Ende" type="text" class="datepick " id="datepicker_4">
											<button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="dp_button_2">Aktualisieren</button>
											<button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="rst_btn_2">R&uuml;ckg&auml;ngig</button>
										<div class="divider"></div>';
									}
									else if($_POST['chart'] == 'heatmap') {
										$content .=
										'<div class="divider"></div>
											<p>Filter:</p>
											<input placeholder="Beginn" type="text" class="datepick " id="datepicker_5">
											<input placeholder="Ende" type="text" class="datepick " id="datepicker_6">
											<button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="dp_button_3">Aktualisieren</button>
											<button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="rst_btn_3">R&uuml;ckg&auml;ngig</button>
										<div class="divider"></div>';
									}
	$content .=
								'</div>
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
								'.$table.'
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
		<script>';
			if ($_POST['chart'] == 'barchart') {
			$content .= 'var barchart_data = '.$bar_chart_data.';';
			}
			else if ($_POST['chart'] == 'linechart') {
				$content .= 'var linechart_data = ['.$lineChart.'];
				var js_activity = ['.$lineChartArray.'];';
			}
			else if ($_POST['chart'] == 'heatmap') {
				$content .= 'var heatmap_data = '.$heatmap_data.';
				var js_heatmap = Object.entries('.$js_heatmap.');';
			}
			else if ($_POST['chart'] == 'treemap') {
				$content .= 'var treemap_data = '.$treemap_data.';';
			}






		$content .= '</script>
		<!-- Barchart, linechart, heatmap and treemap are loaded. Must be included after the data-variables.-->';
		if ($_POST['chart'] == 'barchart') {
			$content .= '<script>'.file_get_contents('js/lemo_barchart.js').'</script>';
		}
		else if ($_POST['chart'] == 'linechart') {
			$content .= '<script>'.$linechart_js_string.'</script>';
		}
		else if ($_POST['chart'] == 'heatmap') {
			$content .= '<script>'.file_get_contents('js/lemo_heatmap.js').'</script>';
		}
		else if ($_POST['chart'] == 'treemap') {
			$content .= '<script>'.file_get_contents('js/lemo_treemap.js').'</script>';
		}

		$content .= '
		<!-- General functions of the plugin. Must be included after the JS-files of the charts. -->
		<script>'.file_get_contents('js/lemo_view.js').'</script>
	</body>
	</html>';
}






//set filename and type of file for the download
header("Content-type: text/html");
header("Content-Disposition: attachment; filename=lemo4moodle_".$heute_filename.".html");
echo $content;
?>
