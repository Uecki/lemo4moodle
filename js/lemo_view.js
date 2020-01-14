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

	/*
	//Trigger file input when clicking icon.
	$('#addIcon').click( function(){
		$('#file_merge').trigger('click');
	})
	*/

	//Arrays for comparing timespans
	//var firstTimestamp = [];
	//var lastTimestamp = [];

	// Empty the selected files on load.
	$('#file_merge').val('');

	/*
	//Variable that stores the selected files in an array
	var filesSelected = new Array();

	//Variable that stores the html elements for each file-input.
	var inputArray = new Array();
	inputArray.push('<li><p class = "black-text"><i class="material-icons medium" id="addIcon">add</i></p><input type="file" style="display:none;"accept=".html" name="fileMerge" id="file_merge'+ inputArray.length.toString() +'"></li>');
	*/

	//Display filenames of selected files.
	$('#file_merge').change(function(){

		/*
		while ($('#fileList').firstChild) {
    myNode.removeChild($('#fileList').firstChild);
  	}

		for (var i = 0; i < inputArray.length; i++) {
			$('#fileList').append(inputArray[i]);
		}
		*/

		// Variables for html elements
		var input = document.getElementById('file_merge');
	  var output = document.getElementById('file_merge_filenames');

		//$('#fileSelection').append('<input type="file" name="file[]"/>');

		// Clear previous filename list.
		$('#file_merge_filenames').empty();
		$('#file_merge_timespan').empty();

		// Fill div with elements containing the filename.
	  //$( '#file_merge_filenames' ).append('<ul>');
		for (var i = 0; i < input.files.length; ++i) {
				$( '#file_merge_filenames' ).append('<li class="black-text">Datei '+ (i+1) + ': ' + input.files[i].name + '</li><br>');
	  }
	  //$( '#file_merge_filenames' ).append('</ul>');

		//Fill div with timespans.
		for (var i = 0; i < input.files.length; ++i) {
			//$( '#file_merge_timespan' ).append('<li class="black-text">'+input.files[i].name+'</li><br>');
			// read file to get the timespan of the datasets.
			readFile(input.files[i], function(e) {
				var fileStringDate = e.target.result;

				if(fileStringDate.includes('var firstDate ')) {
					var root1 = fileStringDate.indexOf('var firstDate =');
					var start1 = fileStringDate.indexOf('"', root1);
					var end1 = fileStringDate.indexOf('"', start1+1);
					var date1 = fileStringDate.substring(start1+1, end1);
					//firstTimestamp.push(new Date(date1));

					var root2 = fileStringDate.indexOf('var lastDate =');
					var start2 = fileStringDate.indexOf('"', root2);
					var end2 = fileStringDate.indexOf('"', start2+1);
					var date2 = fileStringDate.substring(start2+1, end2);
					//lastTimestamp.push(new Date(date2));
					$( '#file_merge_timespan' ).append('<li class="black-text">Zeitraum: ' + date1 + ' - ' + date2 + '</li><br>');
					//console.log(firstTimestamp + lastTimestamp);
				}
				else {
					$( '#file_merge_timespan' ).append('<li class="black-text">Kein Zeitraum vorhanden</li><br>');
				}
			});
	  }
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
var linechartData = "";
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
						if (!(linechartData).includes(item.toString())){
							linechartData += "[" + item.toString() + "],";
						}
						//Replace last index with ']' if last element is reached.
						if (dataArray[dataArray.length-1] == item && loop == (files.length-1)){
							//linechartData = linechartData.replace(/,([^,]*)$/, "$1");
							linechartData = linechartData.substring(1, linechartData.lastIndexOf("],"));
							var linechartDataArray = new Array();
							var tempArray = linechartData.split("],[");
							tempArray.forEach(function(it){
								var tempElements1 = it.split(",");
								var tempElements2 = [it.substring(1, it.lastIndexOf(")")), tempElements1[3], tempElements1[4], tempElements1[5]];
								linechartDataArray.push(tempElements2);
							});
							linechartData = "";
							//console.log(linechartData);
							//console.log(linechartDataArray);
							var jsonArray = JSON.stringify(linechartDataArray);
							//console.log(jsonArray);
							$("#allCharts1").val('true');
							$("#mergeData1").val(jsonArray);
							$("#download_form_1").submit();
							$("#mergeData1").val('');
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
			//console.log(linechartData);
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
