<?php
#create_html.php creates a html file containing the recent data and provides a download link for it

# include the config 
include 'config.php';


# include once moodle/report/outline/index.php and prevent any display function
ob_start();
include_once (moodle_path.'/report/outline/index.php');
include_once (moodle_path.'/config.php');
ob_end_clean();

#get courseID, userID and allData (encoded data-arrays in JSON-format)
$courseID = $_POST["id"];
$userID = $_POST["userid"];
$allData = json_decode($_POST["data"], true);

error_log($allData, 2, "test.txt");

# get today's date 
$heute = date("d.m.y");

# get date for Filename
$heute_filename = date("Y_m_d");

# set path for html file (to save it and to provide a download link)
$pathForHTML = "saved_datasets/".$courseID."_".$userID."/data_".$courseID."_".$userID.".html";

# get data from moodle/report/activity_report (access possible through /report/outline/index.php)
$table = html_writer::table($outlinetable);

/*
# load json file (located on web server)
$file = 'saved_datasets/'.$courseID.'_'.$userID.'/data_'.$courseID.'_'.$userID.'.json';
$existingData = json_decode(file_get_contents($file), true);
*/

$activity_array = array();
$activity_array = $allData[0];
$barchart_array = $allData[1];
$treemap_array  = $allData[2];



# create lineChart data
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




#create bar chart data
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


#create treemap data
$i = 1;
$nodeTitle; #variable for node title
$leng2 = count($treemap_array);
$treemap_data_array = array();
$treemap_data = 
	"['Name', 'Parent', 'Size', 'Color'],
		['Global', null, 0, 0],
			['Dateien', 'Global', 0, 0],";

foreach($treemap_array as $tree){
	#if-clause for node title. (Maybe) To be expanded for forum, chat and assignments.
	if ($tree[1] == 'content') {
		$nodeTitle = 'Dateien';
	}
	#else if ()...
	
	if ($i < $leng ){
		$treemap_data .= "['".$tree[0]."', '".$tree[1]."', ".$tree[2].", ".$tree[3]."],";
	}
	if($i == $leng ){
		$treemap_data .= "['".$tree[0]."', '".$tree[1]."', ".$tree[2].", ".$tree[3]."]";
	}
	$i++;
}




# filter array
$js_activity = json_encode($activity_array, JSON_NUMERIC_CHECK);


$content = '<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ActivityGraph</title>

    <!-- Datepicker jQuery -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- Google Charts -->
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://www.google.com/jsapi"></script>

    <!-- Materialize CSS Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	
	<!-- Materialize CSS  Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>

    <!-- Google Charts - Draw Charts -->
    <script>


        // Load Charts and the corechart package.
        google.charts.load("current", { "packages": ["bar", "line", "treemap", "corechart", "controls"] });

        // Draw the bar chart when Charts is loaded
        google.charts.setOnLoadCallback(drawBarChart);

        // Draw the line chart when Charts is loaded.
        google.charts.setOnLoadCallback(drawLineChart);
		
		// Draw the treemap chart when Charts is loaded.
		google.charts.setOnLoadCallback(drawTreeMap);



        var activity_chart;

        // Callback that draws the bar chart
        function drawBarChart() {
            var data = google.visualization.arrayToDataTable('.$bar_chart_data.');

            var materialOptions_BarChart = {
                chart: {
                    title: "Zugriffe und Nutzer pro Datei"
                },
                axes: {
                    x: {
                        distance: { label: "Dateiname" } // bottom x-axis.
                    },
                    y: {
                        distance: { label: "Zugriffe" } // Left y-axis.
                    }
                },
                legend: {
                    position: "none"

                },
                bars: "horizontal"
            };

            // Instantiate and draw the bar chart 
            var materialBarChart = new google.charts.Bar(document.getElementById("bar_chart"));
            materialBarChart.draw(data, google.charts.Bar.convertOptions(materialOptions_BarChart));

        }



        // Callback that draws the activity chart
        function drawLineChart() {

            var data = new google.visualization.DataTable();
            data.addColumn("date", "Datum");
            data.addColumn("number", "Zugriffe");
            data.addColumn("number", "eigene Zugriffe")
            data.addColumn("number", "Nutzer");
            data.addRows(['.$lineChart.']);

            //  echo $line_chart_data; ?>

            var options = {
                chart: {
                    title: "Zugriffe und Nutzer pro Tag"
                },
                hAxis: {
                    title: "Datum",
                    format: "d.M.yy"
                }


            };

            activity_chart = new google.visualization.LineChart(document.getElementById("line_chart"));
            activity_chart.draw(data, options);
        }
		
		//Callback that draws the treemap.
	function drawTreeMap() {

		var data = new google.visualization.arrayToDataTable(['.$treemap_data.']);
		
		tree = new google.visualization.TreeMap(document.getElementById("treemap"));

        tree.draw(data, {
          minColor: "#f00",
          midColor: "#ddd",
          maxColor: "#0d0",
          headerHeight: 15,
          fontColor: "black",
          highlightOnMouseOver: true,
		  title: "TreeMap für die Anzahl der Klicks pro Datei. Rechtsklick, um eine Ebene nach oben zu gelangen.",
		  generateTooltip: showTooltipTreemap
        });
		
		function showTooltipTreemap(row, size, value) {
			return "<div style='."'background:#fd9; padding:10px; border-style:solid'".'>" + " Anzahl der Klicks: " + size + " </div>";
		}

	}
	
	
	//Callback that draws all charts on tab change.
	//To be optimized to only load chart for current tab.
	function drawAllCharts() {
		drawBarChart();
		drawLineChart();
		drawTreeMap();
	}



    </script>



    <script>

        $(function () {
            $(".datepick").datepicker({
                /*dateFormat: "mm/dd/yy"*/
                dateFormat: "dd.mm.yy"
            });

        });

    </script>

    <script>
        var js_activity = ['.$lineChartArray.'];

        function toTimestamp(strDate) {
            var datum = Date.parse(strDate);
            return datum / 1000;
        }
        $(document).ready(function () {
            $("#dp_button_2").click(function () {
                var start = document.getElementById("datepicker_3").value;
                var end = document.getElementById("datepicker_4").value;
                /* rewrite date */
                var s = start.split(".");
                start = s[1] + "/" + s[0] + "/" + s[2];
                /* rewrite date */
                var e = end.split(".");
                end = e[1] + "/" + e[0] + "/" + e[2];
                start += " 00:00:00";
                end += " 23:59:59";
               
                var tp_start = toTimestamp(start);
                var tp_end = toTimestamp(end);
               
                if (tp_start <= tp_end) {
                    var activity_data = [];
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
                    });
                    console.log(activity_data);
                    var chartData = activity_data.map(function (it) {
                        var str = it.date;
                        var r = str.split(", ");
                        return [new Date(r[0], r[1], r[2]), it.accesses, it.ownhits, it.users];
                    });
                    console.log(chartData);
                    var data = new google.visualization.DataTable();
                    data.addColumn("date", "Datum");
                    data.addColumn("number", "Zugriffe");
                    data.addColumn("number", "eigene Zugriffe");
                    data.addColumn("number", "Nutzer");
                    data.addRows(chartData);
                    var options = {
                        chart: {
                            title: "Zugriffe und Nutzer pro Tag"
                        },
                        hAxis: {
                            title: "Datum",
                            format: "d.M.yy"
                        }
                    };
                    activity_chart.draw(data, options);
                } else {
                    // Materialize.toast(message, displayLength, className, completeCallback);
                    Materialize.toast("Überprüfen Sie ihre Auswahl (Beginn < Ende)", 3000) // 4000 is the duration of the toast
                    $("#datepicker_3").val("");
                    $("#datepicker_4").val("");
                }

            });
        });
    </script>
    <!-- Reset Charts (BarChart, LineChart) -->
    <script>
        $(document).ready(function () {
            /* Bar Chart - reset button */
            $("#rst_btn_2").click(function () {
                var data = new google.visualization.DataTable();
                data.addColumn("date", "Datum");
                data.addColumn("number", "Zugriffe");
                data.addColumn("number", "eigene Zugriffe")
                data.addColumn("number", "Nutzer");
                data.addRows(['.$lineChart.']);
                var options = {
                    chart: {
                        title: "Zugriffe und Nutzer pro Tag"
                    },
                    hAxis: {
                        title: "Datum",
                        format: "d/M/yy"
                    }
                };
                activity_chart.draw(data, options);
                $("#datepicker_3").val("");
                $("#datepicker_4").val("");
            });


        });
    </script>

    <!-- redraw charts when its tab is clicked -->
    <script>
        $(document).ready(function () {
            $(document).ready(function() {
        
				//Minimalize tabs are being initialized, callback function "drawAllCharts"is executed on tab change
				$("#tabs").tabs({ "onShow": drawAllCharts });
		
			});


		});

    </script>

    <style>
        .brand-logo {
            margin-left: 25px;
        }



        .nav-wrapper {
            background-color: #D92425;
        }

        .tabs .tab a {
            color: #D92425;
            background-color: #ffffff;
        }

        /*text color*/

        .tabs .tab a:hover {

            color: #000;
        }

        /*Text color on hover*/

        .tabs .tab a.active {

            color: #D92425;
        }

        /*Background and text color when a tab is active*/

        .tabs .indicator {
            background-color: #D92425;
        }

        /*Color of underline*/

        .tabs .tab.disabled a {
            color: #D92425;
        }

        .tabs .tab.disabled a:hover {
            color: #D92425;
        }

        button {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        #bar_chart {
            width: 100% !important;
            min-height: 500px !important;
        }

        #line_chart {
            width: 100% !important;
            min-height: 500px !important;
        }

        .col .s9 {
            min-height: 500px !important;
        }

        #chart_div {
            width: 100% !important;
            min-height: 500px !important;
        }

        h3 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            font-weight: bold;
        }

        .chart {
            max-width: 100%;
            min-height: 450px;
        }

        .announcement {
            margin: 0px;
            padding: 5px;
        }

        .announcement-link {
            color: #0d47a1;
        }

        .red-text {
            color: #D92425;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="col s12 yellow darken-1 announcement">
            <p class="center-align">
                <i class="material-icons">announcement</i>
                Internetverbindung erforderlich! Lokale Version (Daten nur bis '.$heute.') Aktuelle Daten unter:
                <a href="https://moodle.hwr-berlin.de/" class="announcement-link">moodle.hwr-berlin.de</a>
            </p>
        </div>
        <nav>
            <div class="nav-wrapper">
                <a onClick="window.location.reload()" class="brand-logo">
                    <i class="material-icons">insert_chart</i>ActivityGraph</a>
                <ul id="nav" class="right hide-on-med-and-down">
                    <li>
                        <a href="http://www.hwr-berlin.de/home/" class="waves-effect waves-light btn white red-text" id="btn_hwr" target="_blank">www.hwr-berlin.de
                        </a>
                    </li>
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
                        <a class="active" href="#chart1">Bar Chart</a>
                    </li>
                    <li class="tab" id="tab_activityChart">
                        <a href="#chart2">Activity Chart</a>
                    </li>
                    <li class="tab disabled" id="tab_heatMap">
                        <a href="#chart3">not finished (heat map)</a>
                    </li>
                    <li class="tab" id="tab_treeMap">
                        <a href="#chart4">TreeMap</a>
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
                                <!-- place filter options here -->
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
                                <p>Filter:</p>
                                <input placeholder="Beginn" type="text" class="datepick " id="datepicker_3">
                                <input placeholder="Ende" type="text" class="datepick " id="datepicker_4">
                                <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="dp_button_2">Aktualisieren</button>
                                <button class="btn waves-effect waves-light grey darken-3 button" type="submit" name="action" id="rst_btn_2">R&uuml;ckg&auml;ngig</button>
                                <div class="divider"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="chart3" class="col s12">
                <div class="row">
                    <div class="col s9 chart">
                        <!-- place chart here -->
                    </div>
                    <div id="options" class="col s3">
                        <div class="row">
                            <div class="input-field col s12">
                                <!-- place filter here -->
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
                                <!-- place filter here -->
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

    <!-- make charts responsive -->
    <script>
        $(window).resize(function () {
            drawBarChart();
            drawLineChart();
			drawTreeMap();
        });

    </script>
</body>

</html>';

//file_put_contents($pathForHTML, $content);
//echo $pathForHTML;



header("Content-type: text/html");
header("Content-Disposition: attachment; filename=activitygraph_".$heute_filename.".html");
echo $content;
?>











