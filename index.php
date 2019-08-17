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
include_once ('db_queries.php');

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

    <!-- Materialize CSS  Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>

    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- activityGraph.css -->
    <link rel="stylesheet" href="activitygraph.css">

      <!-- report_styles.css -->
      <link rel="stylesheet" href="styles.css">

    <!-- Google Charts - Draw Charts -->
    <script>

    
        // Load Charts and the corechart package.
  google.charts.load('current', {
      'packages': ['bar', 'line', 'treemap', 'corechart', 'controls']
  });



	

  // Draw the bar chart when Charts is loaded
  google.charts.setOnLoadCallback(drawBarChart);
 	
  // Draw the line chart when Charts is loaded.
  google.charts.setOnLoadCallback(drawLineChart);

  // Draw the treemap chart when Charts is loaded.
  google.charts.setOnLoadCallback(drawTreeMap);


  var activity_chart;
  
  
  // Callback that draws the bar chart
  function drawBarChart(){
	var data = google.visualization.arrayToDataTable(<?php echo $bar_chart_data; ?>);
                
                        var materialOptions_BarChart = {
                          chart: {
                            title: 'Zugriffe und Nutzer pro Datei'
                          },
                          axes: {
                            x: {
                                distance: {label: 'Dateiname'} // bottom x-axis.
                            },
                            y: {
                                distance: {label: 'Zugriffe'} // Left y-axis.
                            }
                          },
        				  legend: {
        					position: 'none'
        					 
        				  },
						  bars: 'horizontal'
                        };
                
                        // Instantiate and draw the bar chart 
        				var materialBarChart = new google.charts.Bar(document.getElementById('bar_chart'));
                        materialBarChart.draw(data, google.charts.Bar.convertOptions(materialOptions_BarChart));
  
  }

    

  // Callback that draws the activity chart
  function drawLineChart() {
	  
	  var data = new google.visualization.DataTable();
      data.addColumn('date', 'Datum');
      data.addColumn('number', 'Zugriffe');
      data.addColumn('number', 'eigene Zugriffe')
	  data.addColumn('number', 'Nutzer');
      data.addRows([<?php echo $lineChart; ?>]);
      //  echo $line_chart_data; ?>

      var options = {
        chart: {
          title: 'Zugriffe und Nutzer pro Tag'
        },
        hAxis: {
          title: 'Datum',
		  format:'d.M.yy'
        }
       
        
      };

      activity_chart = new google.visualization.LineChart(document.getElementById('line_chart'));
      activity_chart.draw(data, options);
	  
    }
	
	//Callback that draws the treemap.
	function drawTreeMap() {
		var data = new google.visualization.arrayToDataTable([
          ['Location', 'Parent', 'Market trade volume (size)', 'Market increase/decrease (color)'],
          ['Global',    null,                 0,                               0],
          ['America',   'Global',             0,                               0],
          ['Europe',    'Global',             0,                               0],
          ['Asia',      'Global',             0,                               0],
          ['Australia', 'Global',             0,                               0],
          ['Africa',    'Global',             0,                               0],
          ['Brazil',    'America',            11,                              10],
          ['USA',       'America',            52,                              31],
          ['Mexico',    'America',            24,                              12],
          ['Canada',    'America',            16,                              -23],
          ['France',    'Europe',             42,                              -11],
          ['Germany',   'Europe',             31,                              -2],
          ['Sweden',    'Europe',             22,                              -13],
          ['Italy',     'Europe',             17,                              4],
          ['UK',        'Europe',             21,                              -5],
          ['China',     'Asia',               36,                              4],
          ['Japan',     'Asia',               20,                              -12],
          ['India',     'Asia',               40,                              63],
          ['Laos',      'Asia',               4,                               34],
          ['Mongolia',  'Asia',               1,                               -5],
          ['Israel',    'Asia',               12,                              24],
          ['Iran',      'Asia',               18,                              13],
          ['Pakistan',  'Asia',               11,                              -52],
          ['Egypt',     'Africa',             21,                              0],
          ['S. Africa', 'Africa',             30,                              43],
          ['Sudan',     'Africa',             12,                              2],
          ['Congo',     'Africa',             10,                              12],
          ['Zaire',     'Africa',             8,                               10]
        ]);
		
		tree = new google.visualization.TreeMap(document.getElementById('treemap'));

        tree.draw(data, {
          minColor: '#f00',
          midColor: '#ddd',
          maxColor: '#0d0',
          headerHeight: 15,
          fontColor: 'black',
          showScale: true
        });

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
                    /*dateFormat: 'mm/dd/yy'*/
                    dateFormat: 'dd.mm.yy'
                });

            });
        
    </script>

    <script>
        <?php
            $js_activity = json_encode($finalLineChartObject, JSON_NUMERIC_CHECK);
		    echo "var js_activity = ". $js_activity . ";\n";
		    $js_barchart = json_encode($barchart, JSON_NUMERIC_CHECK);
		    echo "var js_barchart = ". $js_barchart . ";\n";
		?>
		function toTimestamp(strDate){
			var datum = Date.parse(strDate);
			return datum/1000;
		}
        $(document).ready(function() {
            $('#dp_button_2').click(function() {
                var start = document.getElementById('datepicker_3').value;
        		var end = document.getElementById('datepicker_4').value;
                /* rewrite date */
                s = start.split('.');
                start = s[1]+'/'+s[0]+'/'+s[2];
                /* rewrite date */
                e = end.split('.');
                end = e[1]+'/'+e[0]+'/'+e[2];
				start += ' 00:00:00';
				end += ' 23:59:59';
				var tp_start = toTimestamp(start);
				var tp_end = toTimestamp(end);
                if (tp_start <= tp_end){
                    var activity_data = [];
                    js_activity.forEach(function(item) {
                        if (item.timestamp >= tp_start && item.timestamp <= tp_end) {
                            activity_data.push({
                                date: item.datum,
                                accesses: item.zugriffe,
                                ownhits: item.ownHits,
                                users: item.nutzer
                            });
                        }		
                    });
                    var chartData = activity_data.map(function(it){
                        var str = it.date;
                        r = str.split(', ');
                        return [new Date(r[0], r[1], r[2]), it.accesses, it.ownhits, it.users];
                    });
                    var data = new google.visualization.DataTable();
                        data.addColumn('date', 'Datum');
                        data.addColumn('number', 'Zugriffe');
                        data.addColumn('number', 'eigene Zugriffe');
                        data.addColumn('number', 'Nutzer');
                        data.addRows(chartData);
                    var options = {
                        chart: {
                            title: 'Zugriffe und Nutzer pro Tag'
                        },
                        hAxis: {
                            title: 'Datum',
                            format:'d.M.yy'
                        }
                    };
                    activity_chart.draw(data, options);   
                }else{
                    // Materialize.toast(message, displayLength, className, completeCallback);
                    Materialize.toast('Überprüfen Sie ihre Auswahl (Beginn < Ende)', 3000) // 4000 is the duration of the toast
                    $('#datepicker_3').val("");
                    $('#datepicker_4').val("");
                }
				
            });
        });
    </script>
	
    <!-- Reset Charts (BarChart, LineChart) -->
    <script>
    $(document).ready(function() {
		
        /* Bar Chart - reset button */
        $('#rst_btn_1').click(function() {
        /* do something */
        });
        $('#rst_btn_2').click(function() {
            var data = new google.visualization.DataTable();
				data.addColumn('date', 'Datum');
				data.addColumn('number', 'Zugriffe');
                data.addColumn('number', 'eigene Zugriffe')
			   	data.addColumn('number', 'Nutzer');
				data.addRows([<?php echo $lineChart; ?>]);
            var options = {
                chart: {
                    title: 'Zugriffe und Nutzer pro Tag'
                },
                hAxis: {
                    title: 'Datum',
		            format:'d/M/yy'
                }
            };
			activity_chart.draw(data, options); 
            $('#datepicker_3').val("");
            $('#datepicker_4').val("");
			
			//console.log(<?php echo $lineChart; ?>);
        });


    });
    </script>
	 
    <script>
        $(document).ready(function() {
            $('#btn_close').click(function(){
                window.close();
            });
        });
    </script>


    <!-- redraw charts when its tab is clicked -->
    <script>
    $(document).ready(function() {
        
		//Minimalize tabs are being initialized, callback function 'drawAllCharts' is executed on tab change
		$('#tabs').tabs({ 'onShow': drawAllCharts });
		
	});
    </script>


	<!-- HTML download buttons for barchart/linechart tabs -->
    <script>
        $(document).ready(function() {
            $('#html_btn_1').click(function() {
				document.getElementById("download_form_1").submit();
			});
			
			/* 
		    $('.ajax').click(function(){
                $.ajax({
                    type: 'POST',
                    url: 'create_html.php',
                    data: {id: '<?php echo $courseID?>', userid:'<?php echo $userID?>'},
                    success: function (response) {//response is value returned from php (for your example it's "bye bye"
                        
                        //console.log(response);
                        // echo download link in PHP --> response will be the download link
						
                        $("#html_btn_1").attr("href", response);
                        //$("#html_btn_1").html('Download HTML');
                        $("#html_btn_2").attr("href", response);
                        //$("#html_btn_2").html('Download HTML');
                    }
                });
            });
			*/
			
			$('#html_btn_2').click(function() {
				document.getElementById("download_form_2").submit();
			});
        });
    </script>


</head>

<body>
    <div class="container-fluid">
        <nav>
            <div class="nav-wrapper">
                <a onClick="window.location.reload()" class="brand-logo">
                    <i class="material-icons medium">insert_chart</i>ActivityGraph</a>
                <ul id="nav" class="right hide-on-med-and-down">
                    <li>
                        <a href="http://www.hwr-berlin.de/home/" class="waves-effect waves-light btn white red-text" id="btn_hwr" target="_blank">www.hwr-berlin.de</a>
                    </li>
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
                    <li class="tab disabled" id="tab_heatMap">
                        <a href="#chart3">not finished (heat map)</a>
                    </li>
                    <li class="tab" id="tab_treeMap">
                        <a href="#chart4">not finished (tree map)</a>
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
                                <div class="divider"></div>
                                <p>Datensicherung:</p>
								
                                <!-- Button nicht mehr nötig.
								<a href="<?php echo 'saved_datasets/'.$courseID.'_'.$userID.'/data_'.$courseID.'_'.$userID.'.json'; ?>" download="<?php echo 'activitygraph_data_'.$courseID.'_'.$userID.'.json'; ?>" class="btn waves-effect waves-light grey darken-3 button">Raw Data (JSON)</a>
								-->
								
                                <form action='create_html.php' method='post' id='download_form_1'>
									<a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_1">HTML Download</a>
									<!-- Variables that are to be posted to create_html.php.  -->
									<input type='hidden' value='<?php echo $courseID ?>' name='id'>
									<input type='hidden' value='<?php echo $userID ?>' name='userid'>
									<input type='hidden' value='<?php echo $allData ?>' name='data'>
									
								</form>
								<!--
								<a href="create_html.php" download="test.html" class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_1">HTML Download</a>
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
                                    <a href="<?php echo 'saved_datasets/'.$courseID.'_'.$userID.'/data_'.$courseID.'_'.$userID.'.json'; ?>" download="<?php echo 'activitygraph_data_'.$courseID.'_'.$userID.'.json'; ?>" class="btn waves-effect waves-light grey darken-3 button">Raw Data (JSON)</a>
									-->
									
									<form action='create_html.php' method='post' id='download_form_2'>
										<a class="btn waves-effect waves-light grey darken-3 button ajax" id="html_btn_2">HTML Download</a>
										<!-- Variables that are to be posted to create_html.php.  -->
										<input type='hidden' value='<?php echo $courseID ?>' name='id'>
										<input type='hidden' value='<?php echo $userID ?>' name='userid'>
										<input type='hidden' value='<?php echo $fullData ?>' name='data'>
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
                       <!-- place chart here -->
                    </div>
                        <div id="options" class="col s3">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input placeholder="Beginn" type="text" class="datepick " id="datepicker_5">
                                    <input placeholder="Ende" type="text" class="datepick " id="datepicker_6">
                                    <button class="btn waves-effect waves-light grey button" type="submit" name="action">Aktualisieren</button>
                                    <button class="btn waves-effect waves-light grey button" type="submit" name="action">R&uuml;ckg&auml;ngig</button>
                                    <div class="divider"></div>
                                    <a href="<?php echo 'saved_datasets/'.$courseID.'_'.$userID.'/data_'.$courseID.'_'.$userID.'.json'; ?>" download="<?php echo 'activitygraph_data_'.$courseID.'_'.$userID.'.json'; ?>"><button class="btn waves-effect waves-light grey button" type="submit" name="action" id="json_btn_3">JSON Download</button></a>
                                    <a href="#" download="test.html" class="btn waves-effect waves-light grey button" id="html_btn_2"><!--HTML erzeugen-->Download HTML</a>
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
                                    <input placeholder="Beginn" type="text" class="datepick " id="datepicker_7">
                                    <input placeholder="Ende" type="text" class="datepick " id="datepicker_8">
                                    <button class="btn waves-effect waves-light grey button" type="submit" name="action">Aktualisieren</button>
                                    <button class="btn waves-effect waves-light grey button" type="submit" name="action">R&uuml;ckg&auml;ngig</button>
                                    <div class="divider"></div>
                                    <a href="<?php echo 'saved_datasets/'.$courseID.'_'.$userID.'/data_'.$courseID.'_'.$userID.'.json'; ?>" download="<?php echo 'activitygraph_data_'.$courseID.'_'.$userID.'.json'; ?>"><button class="btn waves-effect waves-light grey button" type="submit" name="action" id="json_btn_4">JSON Download</button></a>
                                    <a href="<?php echo 'saved_datasets/data_'.$courseID.'_'.$userID.'.html'; ?>" download="<?php echo 'activitygraph_html_'.$courseID.'_'.$userID.'.html'; ?>"><button class="btn waves-effect waves-light grey button" type="submit" name="action" id="html_btn_4">HTML Download</button></a>
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

<!-- make charts responsive -->
<script>
$(window).resize(function(){
  drawBarChart();
  drawLineChart();
});

</script>
</body>
</html>





