/*JS-file for all global functions of the plugin.*/

$(document).ready(function() {
	
	// Redraw charts when page is resized.
	$(window).resize(function(){
		drawBarChart();
		drawLineChart();
		drawHeatMap();
		drawTreeMap();
	});

	//Closes the window when the button "Schließen" is clicked.
	$('#btn_close').click(function(){
		window.close();
	});
	
	//Minimalize tabs are being initialized, callback function 'drawAllCharts' is executed on tab change
	$('#tabs').tabs({ 'onShow': drawAllCharts });
	
});

// Load Charts and the corechart package.
google.charts.load('current', {
  'packages': ['bar', 'line', 'treemap', 'corechart', 'controls']
});

// Draw all charts when Charts is loaded. (Even the Highchart, which is not from Google Charts).
google.charts.setOnLoadCallback(drawAllCharts);

$(function () {
	$(".datepick").datepicker({
		/*dateFormat: 'mm/dd/yy'*/
		dateFormat: 'dd.mm.yy'
	});
});

//Function to get the timestamp of a date-string.
function toTimestamp(strDate){
	var datum = Date.parse(strDate);
	return datum/1000;
}

//Callback that draws all charts.
//To be optimized to only load chart for current tab.
function drawAllCharts() {
	drawBarChart();
	drawLineChart();
	drawTreeMap();
	drawHeatMap();
}