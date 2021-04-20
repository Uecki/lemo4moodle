// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * JS file for everything that can be seen on or is related to the barchart tab.
 *
 * The languae strings used here are initialised as variables in index.php.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Finn Ueckert, Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Language file variables.
var selectAll = $('#selectAll').val();
var barchartTitle = $('#barchartTitle').val();
var barchartXLabel = $('#barchartXLabel').val(); // Name.
var barchartYLabel = $('#barchartYLabel').val(); // Accesses.
var barchartUser = $('#linechartColUser').val();
var barchartModule  = $('#barchartModule').val();
var barchartDefaultData = block_lemo4moodle_createBarchartData(barchartData);
var barchartSelectedModuleData = barchartDefaultData;


$(document).ready(function() {

    // Barchart - filter reset button.
    $('#rst_btn_1').click(function() {
        block_lemo4moodle_drawBarchart(barchartDefaultData);
        barchartSelectedModuleData = barchartDefaultData;
        $("#datepicker_1").val("");
        $("#datepicker_2").val("");
    });

    // Filter for barchart.
    $('#dp_button_1').click(function() {

        var startTimestamp = block_lemo4moodle_getStartTimestamp(document.getElementById('datepicker_1').value);
        var endTimestamp = block_lemo4moodle_getEndTimestamp(document.getElementById('datepicker_2').value);
        if (startTimestamp <= endTimestamp) {
            var filteredBarchartData = block_lemo4moodle_createBarchartData(barchartData, startTimestamp, endTimestamp);

            // Check, which module type is displayed and apply the selection to the filtered data.
            if($('select option:selected').text()  == selectAll) {
                barchartSelectedModuleData  = filteredBarchartData;
            } else{
                barchartSelectedModuleData = new Array();
                barchartSelectedModuleData.push(filteredBarchartData[0]);
                filteredBarchartData.forEach(function(item){
                    if($('select option:selected').text()  == item[3]) {
                        barchartSelectedModuleData.push(item);
                    }
                });
            }
            block_lemo4moodle_drawBarchart(barchartSelectedModuleData);
        } else {
            Materialize.toast(viewCheckSelection, 3000); // 3000 is the duration of the toast.
            $('#datepicker_1').val("");
            $('#datepicker_2').val("");
        }
    });

    // Download button for barchart tab.
    $('#html_btn_1').click(function() {
        // Opens dialog box.
        $( "#dialog" ).dialog("open");
    });


    // Change event for the select field.
    // Depending on which option is selected, different values are displayed by the chart.
    $('#barchart_select_module').change(function() {

        // Reset the filter field.
        $('#datepicker_1').val("");
        $('#datepicker_2').val("");

        // Reset the selection variable.
        if($('select option:selected').text()  == selectAll) {
            barchartSelectedModuleData  = barchartDefaultData;
        } else{
            barchartSelectedModuleData = new Array();
            barchartSelectedModuleData.push(barchartDefaultData[0]);
            barchartDefaultData.forEach(function(item){
                if($('select option:selected').text()  == item[3]) {
                    barchartSelectedModuleData.push(item);
                }
            });
        }
        block_lemo4moodle_drawBarchart(barchartSelectedModuleData);
    });

    // Redraw charts when page is resized.
    $(window).resize(function() {
        block_lemo4moodle_drawBarchart(barchartSelectedModuleData);
    });

    // Minimalize tabs are being initialized, callback function
    // 'block_lemo4moodle_drawBarchart' is executed on tab change.
    $('#tabs').tabs({ 'onShow': block_lemo4moodle_drawBarchart(barchartDefaultData) });

});

/**
 * Function that rearranges and processes the data enhanced by block_lemo4moodle_addEmptyLinechartData to fit into the right format for plotly.
 * See plotly documentation for heatmap: https://plot.ly/javascript/heatmaps/
 *
 * @method block_lemo4moodle_createBarchartData
 * @param Array dataArray Array of objects gotten from the database via lemo_db_queries.
 * @param date startTimestamp Optional parameter, used as starting date if the data is to be filtered.
 * @param date endTimestamp Optional parameter, used as ending date if the data is to be filtered.
 */
function block_lemo4moodle_createBarchartData(dataArray, startTimestamp = 0, endTimestamp = 0) {

    // XLabel = name; YLabel = accesses.
    var barchartDataFinal = [[barchartXLabel, barchartYLabel, barchartUser, barchartModule]];

    // Array that serves to store the amount of times a file or activity was accessed and by how many users it was accessed.
    var counterArray = [];

    // Iterate through the data.
    dataArray.forEach(function(item) {

        // Change date format.
        var splitDate = item.date.split("-");
        var mergedDate = new Date(splitDate[2] + ", " + splitDate[1] + ", " + splitDate[0]);
        var date = splitDate[2] + ", " + splitDate[1] + ", " + splitDate[0];
        var timestamp = Date.parse(mergedDate) / 1000;

        // Check, if the function was called by the filter or not.
        // Info: Every distinct object gets its contextid as id, so that all corresponding actions can be assigned to it.
        // Not the filter:
        if(startTimestamp == 0 && endTimestamp == 0) {

            // Check, if the element already exists in the counter Array.
            if(counterArray.find(elem => elem.id === item.contextid)) {
                var matchedElement = counterArray.find(elem => elem.id === item.contextid);
                matchedElement.counter += 1;
                if(!(matchedElement.users.includes(item.userid))) {
                    matchedElement.users.push(item.userid);
                }
            } else {
                counterArray.push({id:item.contextid, counter:1, users:[item.userid], module:item.component, name:item.name});
            }

        // Filter:
        } else if (timestamp >= startTimestamp && timestamp <= endTimestamp) {

            // Check, if the element already exists in the counter Array.
            if(counterArray.find(elem => elem.id === item.contextid)) {
                var matchedElement = counterArray.find(elem => elem.id === item.contextid);
                matchedElement.counter += 1;
                if(!(matchedElement.users.includes(item.userid))) {
                    matchedElement.users.push(item.userid);
                }
            } else {
                counterArray.push({id:item.contextid, counter:1, users:[item.userid], module:item.component, name:item.name});
            }

        }
    });

    counterArray.forEach(function(item) {
        barchartDataFinal.push([item.name, item.counter, item.users.length, item.module]);
    });

    // Sort the array of arrays by number of clicks per activity/file.
    barchartDataFinal = barchartDataFinal.sort(function(a, b) {
        return b[1] - a[1];
    });

    return barchartDataFinal;
}

/**
 * Callback function that draws the barchart.
 * See google charts documentation for barchart: https://developers.google.com/chart/interactive/docs/gallery/barchart.
 * @method block_lemo4moodle_drawBarchart
 */
function block_lemo4moodle_drawBarchart(data) {

    // Generate x values for clicks.
  var xValuesClicks = [];
	var counter = data.length - 1;
    while (counter >= 1) {
		xValuesClicks.push(
            data[counter][1]
		)
		counter-- ;
    }

    // Generate x value for users.
	var xValuesUser = [];
	var counter = data.length - 1;
    while (counter >= 1) {
		xValuesUser.push(
            data[counter][2]
		)
		counter-- ;
    }

    // Generate y value.
    var yValues = [];
    counter = data.length - 1;
    while (counter >= 1) {
        yValues.push(
			data[counter][0]
        )
        counter-- ;
    }

    //Clicks
	var trace1 = {
	  x: xValuesClicks,
	  y: yValues,
	  type: 'bar',
      orientation: 'h',
	  name: barchartYLabel,
	  marker: {
		color: 'rgb(49,130,189)',
        line: {
            color: 'rgb(0,0,0)',
            width: 1.5
        }
	  }
	};

    //User
	var trace2 = {
	  x: xValuesUser,
	  y: yValues,
	  type: 'bar',
      textposition: 'auto',
      orientation: 'h',
	  name: barchartUser,
	  marker: {
		color: 'rgb(0, 153, 0)',
        line: {
            color: 'rgb(0,0,0)',
            width: 1.5
        }
	  }
	};

	var dataFinal = [trace1, trace2];
	var heightPlot = $('#barchart').height();;
	if(data.length > 25)
		heightPlot+= 10*data.length;
	var layout = {
		height: heightPlot,
        title: barchartTitle,
        barmode: 'group',
        margin: {
            l: 200,
            r: 10,
            t: 100,
            b: 70
        }
	};


	Plotly.newPlot('barchart', dataFinal, layout);

}

/**
 * Function to initialise the filter that makes it possible to filter all shown data
 * depending on the chosen module (content type).
 *
 * @method block_lemo4moodle_initFilterBarchart
 * @param Array data Barchart dataarray that is created by lemo_db_queries.
 */
function block_lemo4moodle_initFilterBarchart(data) {
    // Create selection list for each module type contained in the course.
    var courseModules = new Array();
    data.forEach(function(item){

        // Prevent header from being in the list.
        if(item[3] == barchartModule) {

        } else {
            if(courseModules.includes(item[3])) {

            } else {
                courseModules.push(item[3]);
            }
        }
    });
    courseModules.forEach(function(item, index) {
        // Create element in the select field.
        $('#barchart_select_module').append('<option value="' + index +
                '" id="barchart_module' + index + '">' + item + '</option>');
    });
}
