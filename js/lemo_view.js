/*JS-file for all global functions of the plugin.*/


$(document).ready(function() {
	
	// Redraw charts when page is resized.
	$(window).resize(function(){
		drawAllCharts();
	});

	//Closes the window when the button "Schließen" is clicked.
	$('#btn_close').click(function(){
		window.close();
	});
	
	//Minimalize tabs are being initialized, callback function 'drawAllCharts' is executed on tab change
	$('#tabs').tabs({ 'onShow': drawAllCharts });
	
	//Initializing the dialog box shown before the download.
	$( "#dialog" ).dialog({
		autoOpen: false, 
		buttons: [
			{
				text: "Dieser Graph",
				click: function() {
					$(this).dialog("close");
					if ($(".active").attr('id') == 'tab1'){
						document.getElementById("allCharts1").value = 'false';
						document.getElementById("download_form_1").submit();
					}
					else if ($(".active").attr('id') == 'tab2'){
						document.getElementById("allCharts2").value = 'false';
						document.getElementById("download_form_2").submit();
					}
					else if ($(".active").attr('id') == 'tab3'){
						document.getElementById("allCharts3").value = 'false';
						document.getElementById("download_form_3").submit();
					}
					else if ($(".active").attr('id') == 'tab4'){
						document.getElementById("allCharts4").value = 'false';
						document.getElementById("download_form_4").submit();
					}
				}
			},
			{
				text: "Alle Graphen",
				click: function() {
					$(this).dialog("close");	
					if ($(".active").attr('id') == 'tab1'){
						document.getElementById("allCharts1").value = 'true';
						document.getElementById("download_form_1").submit();
					}
					else if ($(".active").attr('id') == 'tab2'){
						document.getElementById("allCharts2").value = 'true';
						document.getElementById("download_form_2").submit();
					}
					else if ($(".active").attr('id') == 'tab3'){
						document.getElementById("allCharts3").value = 'true';
						document.getElementById("download_form_3").submit();
					}
					else if ($(".active").attr('id') == 'tab4'){
						document.getElementById("allCharts4").value = 'true';
						document.getElementById("download_form_4").submit();
					}
				}
			}
		]
	});
	
});

// Load Charts and the corechart package.
google.charts.load('current', {
  'packages': ['bar', 'line', 'treemap', 'corechart', 'controls']
});

// Draw all charts when Charts is loaded. (Even the Highchart, which is not from Google Charts).
google.charts.setOnLoadCallback(drawAllCharts);

//JQuery datepicker funtion (for filter)
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
	if (typeof drawBarChart === "function") {
		drawBarChart();
	}
	if (typeof drawLineChart === "function") {
		drawLineChart();
	}
	if (typeof drawHeatMap === "function") {
		drawHeatMap();
	}
	if (typeof drawTreeMap === "function") {
		drawTreeMap();
	}
}