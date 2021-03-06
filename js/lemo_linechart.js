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
 * JS file for everything that can be seen on or is related to the linechart tab.
 *
 * The languae strings used here are initialised as variables in index.php.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Finn Ueckert, Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Language file variables.
var linechartColDate = $('#linechartColDate').val();
var linechartColAccess = $('#linechartColAccess').val();
var linechartColOwnAccess = $('#linechartColOwnAccess').val();
var linechartColUser = $('#linechartColUser').val();
var linechartColMissingData = $('#linechartColMissingData').val();
var linechartTitle = $('#linechartTitle').val();
var linechartCheckSelection = $('#linechartCheckSelection').val();
var linechartDefaultData = block_lemo4moodle_createLinechartData(linechartData);
var filteredLinechartData = linechartDefaultData;

$(document).ready(function() {

    // Line Chart - reset button.
    $('#rst_btn_2').click(function() {
        block_lemo4moodle_drawLinechart(linechartDefaultData);
        filteredLinechartData = linechartDefaultData;
        $("#datepicker_3").val("");
        $("#datepicker_4").val("");
    });

    // Filter for Linechart.
    $('#dp_button_2').click(function() {
        var startTimestamp = block_lemo4moodle_getStartTimestamp(document.getElementById('datepicker_3').value);
        var endTimestamp = block_lemo4moodle_getEndTimestamp(document.getElementById('datepicker_4').value);
        if (startTimestamp <= endTimestamp) {
            filteredLinechartData = block_lemo4moodle_createLinechartData(linechartData, startTimestamp, endTimestamp);
            block_lemo4moodle_drawLinechart(filteredLinechartData);
        } else {
            Materialize.toast(viewCheckSelection, 3000); // 3000 is the duration of the toast.
            $('#datepicker_3').val("");
            $('#datepicker_4').val("");
        }
    });

    // Download button for linechart tab.
    $('#html_btn_2').click(function() {
        // Opens dialog box.
        $( "#dialog" ).dialog( "open" );
    });

    // Redraw charts when page is resized.
    $(window).resize(function() {
        block_lemo4moodle_drawLinechart(linechartDefaultData);
    });

    // Minimalize tabs are being initialized, callback function
    // 'block_lemo4moodle_drawLinechart' is executed on tab change.
    $('#tabs').tabs({ 'onShow': block_lemo4moodle_drawLinechart(linechartDefaultData) });

});


/**
 * Function that adds missing days to the query result and reformats it a bit.
 * See plotly documentation for heatmap: https://plot.ly/javascript/heatmaps/
 *
 * @method block_lemo4moodle_createLinechartData
 * @param Object queryResult Query result fetched from the server and given to js in index.php.
 */
function block_lemo4moodle_addEmptyLinechartData(queryResult) {

    // Array that stores the final datasets.
    var data = [];

    // Array that stores only transformed datasets from the database.
    var queryResultArray = [];

    // Change date format and create a timestamp for each dataset.
    // Then add these values to a new array.
    queryResult.forEach(function(item) {
        // Change date format.
        var splitDate = item.date.split("-");
        var mergedDate = new Date(splitDate[2] + ", " + splitDate[1] + ", " + splitDate[0]);
        var date = splitDate[2] + ", " + splitDate[1] + ", " + splitDate[0];
        var timestamp = Date.parse(mergedDate) / 1000;
        // Indices of item: 0->date, 1->allhits, 2->users, ->ownhits.
        queryResultArray.push({date:date, allhits:item.allhits, users:item.users, ownhits:item.ownhits, timestamp:timestamp});
    });

    // Fill in emtpy datasets. This is necessary for correctly displaying the plotly chart.
    for(var i = 0; i < queryResultArray.length; i++) {

        // Add dataset with data to a new array.
        data.push({date:new Date(queryResultArray[i].date), allhits:queryResultArray[i].allhits,
                users:queryResultArray[i].users, ownhits:queryResultArray[i].ownhits, timestamp:queryResultArray[i].timestamp});

        // Check, if last element is reached. Otherwise, there would be an error at the last element.
        if(i != queryResultArray.length - 1) {

            // Use two date variables to check for the days in between data.
            var nextDay = new Date(queryResultArray[i + 1].date);
            var currentLoopDay = new Date(queryResultArray[i].date);
            currentLoopDay.setDate(currentLoopDay.getDate() + 1);

            // Create empty datasets, while the date is not already used for data.
            while(currentLoopDay < nextDay) {
                var emptyData = {date:new Date(currentLoopDay), allhits:0, users:0, ownhits:0, timestamp:Date.parse(new Date(currentLoopDay)) / 1000};
                data.push(emptyData);
                currentLoopDay.setDate(currentLoopDay.getDate() + 1);
            }
        }
    }
    return data;
}

/**
 * Function that rearranges and processes the data enhanced by block_lemo4moodle_addEmptyLinechartData to fit into the right format for plotly.
 * See plotly documentation for heatmap: https://plot.ly/javascript/heatmaps/
 *
 * @method block_lemo4moodle_createLinechartData
 * @param Array dataArray Data object processed by block_lemo4moodle_addEmptyLinechartData.
 * @param date startTimestamp Optional parameter, used as starting date if the data is to be filtered.
 * @param date endTimestamp Optional parameter, used as ending date if the data is to be filtered.
 */
function block_lemo4moodle_createLinechartData(dataArray, startTimestamp = 0, endTimestamp = 0) {

    var preprocessedData = block_lemo4moodle_addEmptyLinechartData(dataArray);

    var linechartDataFinal = [];

    preprocessedData.forEach(function(item) {

        // Check, if the function was called by the filter or not.
        if(startTimestamp == 0 && endTimestamp == 0) {
            linechartDataFinal.push({date:item.date, allhits:item.allhits, ownhits:item.ownhits, users:item.users});
        } else if (item.timestamp>= startTimestamp && item.timestamp <= endTimestamp) {

            linechartDataFinal.push({date:item.date, allhits:item.allhits, ownhits:item.ownhits, users:item.users});
        }
    });

    return linechartDataFinal;
}

/**
 * Callback function that draws the linechart.
 * See google charts documentation for linechart: https://developers.google.com/chart/interactive/docs/gallery/linechart
 * @method block_lemo4moodle_drawLinechart
 * @param array data Data array that contains the data to be drawn as a linechart.
 */
function block_lemo4moodle_drawLinechart(data) {
    // Variable that stores the start of each month for every month in the dataset.
    var monthCounter = [];

    // Generate x value.
    var xValuesDates = [];
    for (var i = 0; i < data.length; i++) {
        var dateObject = data[i].date;
        var day = dateObject.getDate();
        var month = (dateObject.getMonth() + 1);
        var year = dateObject.getFullYear();
        xValuesDates.push(day + '.' + month + '.' + year);

        if (day == 1) {
            monthCounter.push(day + '.' + month + '.' + year);
        }
    }

    // Generate y values for overall accesses.
    var yValuesAccesses = [];
    for (var i = 0; i < data.length; i++) {
        if(data[i].allhits == -1) {
            yValuesAccesses.push("");
        } else {
            yValuesAccesses.push(data[i].allhits);
        }
    }

    // Generate y values for own accesses.
    var yValuesOwnAccesses = [];
    for (var i = 0; i < data.length; i++) {
        if(data[i].ownhits == -1) {
            yValuesOwnAccesses.push("");
        } else {
            yValuesOwnAccesses.push(data[i].ownhits);
        }
    }

    // Generate y values for number of users.
    var yValuesUsers = [];
    for (var i = 0; i < data.length; i++) {
        if(data[i].users == -1) {
            yValuesUsers.push("");
        } else {
            yValuesUsers.push(data[i].users);
        }
    }

    // Generate y values for missing data.
    var yValuesMissingData = [];
    for (var i = 0; i < data.length; i++) {
        if(data[i].allhits == -1) {
            yValuesMissingData.push(0);
        } else {
            yValuesMissingData.push("");
        }
    }

    var trace1 = {
        x: xValuesDates,
        y: yValuesAccesses,
        type: 'scatter',
        name: linechartColAccess
    };

    var trace2 = {
        x: xValuesDates,
        y: yValuesOwnAccesses,
        type: 'scatter',
        name: linechartColOwnAccess
    };

    var trace3 = {
        x: xValuesDates,
        y: yValuesUsers,
        type: 'scatter',
        name: linechartColUser
    };

    var trace4 = {
        x: xValuesDates,
        y: yValuesMissingData,
        type: 'scatter',
        name: linechartColMissingData
    }

    var completeData = [trace1, trace2, trace3, trace4];

    var layout = {
        title: linechartTitle,
        xaxis: {
            title: linechartColDate,
            tickvals: monthCounter,
            ticktext: monthCounter
        }
    };

    Plotly.newPlot('linechart', completeData, layout);
}
