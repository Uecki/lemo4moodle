/*JS-file for everything that can be seen on or is related to the barchart-tab. Uses language-strings initialised in index.php.*/

$(document).ready(function() {

	// Barchart - reset button
	$('#rst_btn_1').click(function() {
	/* do something */
	});

	//Download button for barchart tab.
	$('#html_btn_1').click(function() {
		//Opens dialog box.
		$( "#dialog" ).dialog( "open" );
	});

});

// Callback that draws the bar chart (google charts).
//See the google charts documentation for linechart.
function drawBarChart(){
var data = google.visualization.arrayToDataTable(barchart_data);

	var materialOptions_BarChart = {
		chart: {
			title: barchart_title
		},
		axes: {
			x: {
				distance: {label: barchart_xlabel} // bottom x-axis.
			},
			y: {
				distance: {label: barchart_ylabel} // Left y-axis.
			}
		},
		legend: {
			position: 'none'

		},
		bars: 'horizontal'
	};

	// Instantiate and draw the bar chart.
	var materialBarChart = new google.charts.Bar(document.getElementById('barchart'));
	materialBarChart.draw(data, google.charts.Bar.convertOptions(materialOptions_BarChart));

}
