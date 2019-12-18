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

//Initialize the Materialize Modal (PopUp)
$(document).ready(function(){
	 $('.modal').modal();
 });

//Variables for filemerging
var barchart_data_test = "[";
var linechart_data_test = "";
var heatmap_data_test = "[";
var treemap_data_test = "[";

//Function for filemerging
$('#mergeButton').click(function() {
	//$("#modal_error1").text("");
	$("#modal_error2").text("");
	//fileContainer = document.querySelector('#file_container');
	var fileMerge = document.querySelector('#file_merge');
/*
	if(fileContainer.files.length == 0){
		$("#modal_error1").text("Bitte eine zu überschreibende Datei auswählen.");
		return;
	}
	*/
	if(fileMerge.files.length < 2){
		$("#modal_error2").text ("Bitte mindestens zwei Dateien zum Zusammenfügen auswählen.");
		return;
	}
	var files =	fileMerge.files;
	var fileString;
	//const charttype = ["barchart_data", "linechart_data", "heatmap_data", "treemap_data"];
	const charttype = ["linechart_data"];

	//Variable to keep track of loop (for callback).
	var loop=0;

	//Iterate trough each selected file
	for (i=0; i<files.length; i++) {
		//Callback function
		readFile(files[i], function(e) {
			fileString = e.target.result;
			//Iterate through each charttype
			charttype.forEach(function(it){
				//Get the data from the file as an array of strings
				var start = fileString.indexOf("[", fileString.indexOf("var "+ it +" ="));
				var end = fileString.indexOf(";", fileString.indexOf("var " + it + " ="));
				var rawData = fileString.substring(start, end);
				var data = rawData.substring(2, rawData.lastIndexOf("]]"));
				var dataArray;
				if (it == "linechart_data") {
					dataArray = data.split("],[");
				}
				else if(it == "heatmap_data"){
					dataArray = data.split("], [");
				}
				dataArray.forEach( function(item){
					/* Not yet functional
					//Collect barchart data
					if(it == "barchart_data") {
						var lala = fileString.indexOf("Lokale Version erstellt: ");
						var datum = fileString.substring(lala+25, lala+33)
						//console.log();
						if (!(barchart_data_test).includes(item.toString())){
							barchart_data_test += "[" + item.toString() + "],";
						}
						//Replace last index with ']' if last element is reached.
						if (dataArray[dataArray.length-1] == item && loop == (files.length-1)){
							barchart_data_test = barchart_data_test.replace(/,([^,]*)$/, "]$1");
						}
					}
					*/

					//Collect linechart data
					if(it == "linechart_data" && item.toString().length > 2) { //filter out the empty data
						if (!(linechart_data_test).includes(item.toString())){
							linechart_data_test += "[" + item.toString() + "],";
						}
						//Replace last index with ']' if last element is reached.
						if (dataArray[dataArray.length-1] == item && loop == (files.length-1)){
							//linechart_data_test = linechart_data_test.replace(/,([^,]*)$/, "$1");
							linechart_data_test = linechart_data_test.substring(1, linechart_data_test.lastIndexOf("],"));
							var linechart_data_array = new Array();
							var tempArray = linechart_data_test.split("],[");
							tempArray.forEach(function(it){
								var tempElements1 = it.split(",");
								var tempElements2 = [it.substring(1, it.lastIndexOf(")")), tempElements1[3], tempElements1[4], tempElements1[5]];
								linechart_data_array.push(tempElements2);
							});
							linechart_data_test = "";
							//console.log(linechart_data_test);
							//console.log(linechart_data_array);
							var jsonArray = JSON.stringify(linechart_data_array);
							//console.log(jsonArray);
							$("#allCharts1").val('true');
							$("#mergeData1").val(jsonArray);
							$("#download_form_1").submit();
							$("#mergeData1").value = '';
							//console.log($("#mergeData1").val());
						}
					}
					/* Not yet functional
					//Collect heatmap data
					if(it == "heatmap_data" && item.toString().length > 2) { //filter out the empty data
						var subArray = item.toString().split(", ");

						if (!(heatmap_data_test).includes(item.toString())){
							heatmap_data_test += "[" + item.toString() + "],";
						}
						//Replace last index with ']' if last element is reached.
						if (dataArray[dataArray.length-1] == item && loop == (files.length-1)){
							heatmap_data_test = heatmap_data_test.replace(/,([^,]*)$/, "]$1");
						}
					}*/
				});
			});
			//console.log(linechart_data_test);
			loop++;
		});
	}
});

//Callback for the FileReader
function readFile (file, onLoadCallback){
	var reader = new FileReader();
	reader.onload = onLoadCallback;
	reader.readAsText(file);
}
