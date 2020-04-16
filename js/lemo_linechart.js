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
 * @copyright  2020 Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Language file variables.
var linechartColDate = $('#linechartColDate').val();
var linechartColAccess = $('#linechartColAccess').val();
var linechartColOwnAccess = $('#linechartColOwnAccess').val();
var linechartColUser = $('#linechartColUser').val();
var linechartTitle = $('#linechartTitle').val();
var linechartCheckSelection = $('#linechartCheckSelection').val();

$(document).ready(function() {

    // Line Chart - reset button.
    $('#rst_btn_2').click(function() {
        block_lemo4moodle_drawLinechart();
        $("#datepicker_3").val("");
        $("#datepicker_4").val("");
    });

    // Filter for Linechart.
    $('#dp_button_2').click(function() {
        var start = document.getElementById('datepicker_3').value;
        var end = document.getElementById('datepicker_4').value;
        // Rewrite date.
        var s = start.split('.');
        start = s[1] + '/' + s[0] + '/' + s[2];
        // Rewrite date.
        var e = end.split('.');
        end = e[1] + '/' + e[0] + '/' + e[2];
        start += ' 00:00:00';
        end += ' 23:59:59';
        var startTimestamp = block_lemo4moodle_toTimestamp(start);
        var endTimestamp = block_lemo4moodle_toTimestamp(end);
        if (startTimestamp <= endTimestamp) {
            var activityData = [];
            linechartDataArrayFilter.forEach(function(item) {
                if (item.timestamp >= startTimestamp && item.timestamp <= endTimestamp) {
                    activityData.push({
                        date: item.date,
                        accesses: item.accesses,
                        ownhits: item.ownhits,
                        users: item.user
                    });
                }
            });
            var chartData = activityData.map(function(it) {
                var str = it.date;
                var r = str.split(', ');
                return [new Date(r[0], r[1], r[2]), it.accesses, it.ownhits, it.users];
            });
            // Variable that stores the start of each month for every month in the dataset.
            var monthCounter = [];

            // Generate x value.
            var xValuesDates = [];
            for (var i = 0; i < chartData.length; i++) {
                var dateObject = chartData[i][0];
                var day = dateObject.getUTCDate();
                var month = (dateObject.getUTCMonth() + 1);
                var year = dateObject.getUTCFullYear();
                xValuesDates.push(day + '.' + month + '.' + year);

                if (day == 1) {
                    monthCounter.push(day + '.' + month + '.' + year);
                }
            }

            // Generate y values for overall accesses.
            var yValuesAccesses = [];
            for (var i = 0; i < chartData.length; i++) {
                yValuesAccesses.push(chartData[i][1]);
            }

            // Generate y values for own accesses.
            var yValuesOwnAccesses = [];
            for (var i = 0; i < chartData.length; i++) {
                yValuesOwnAccesses.push(chartData[i][2]);
            }

            // Generate y values for number of users.
            var yValuesUsers = [];
            for (var i = 0; i < chartData.length; i++) {
                yValuesUsers.push(chartData[i][3]);
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

            var data = [trace1, trace2, trace3];

            var layout = {
                title: linechartTitle,
                xaxis: {
                    title: linechartColDate,
                    tickvals: monthCounter,
                    ticktext: monthCounter
                }
            };

            Plotly.newPlot('linechart', data, layout);
        } else {
            Materialize.toast(linechartCheckSelection, 3000); // 3000 is the duration of the toast.
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
        block_lemo4moodle_drawLinechart();
    });

    // Minimalize tabs are being initialized, callback function
    // 'block_lemo4moodle_drawLinechart' is executed on tab change.
    $('#tabs').tabs({ 'onShow': block_lemo4moodle_drawLinechart });

});

/**
 * Callback function that draws the linechart.
 * See google charts documentation for linechart: https://developers.google.com/chart/interactive/docs/gallery/linechart
 * @method block_lemo4moodle_drawLinechart
 */
function block_lemo4moodle_drawLinechart() {

    // Variable that stores the start of each month for every month in the dataset.
    var monthCounter = [];

    // Generate x value.
    var xValuesDates = [];
    for (var i = 0; i < linechartDataArray.length; i++) {
        var dateObject = linechartDataArray[i][0];
        var day = dateObject.getUTCDate();
        var month = (dateObject.getUTCMonth() + 1);
        var year = dateObject.getUTCFullYear();
        xValuesDates.push(day + '.' + month + '.' + year);

        if (day == 1) {
            monthCounter.push(day + '.' + month + '.' + year);
        }
    }

    // Generate y values for overall accesses.
    var yValuesAccesses = [];
    for (var i = 0; i < linechartDataArray.length; i++) {
        yValuesAccesses.push(linechartDataArray[i][1]);
    }

    // Generate y values for own accesses.
    var yValuesOwnAccesses = [];
    for (var i = 0; i < linechartDataArray.length; i++) {
        yValuesOwnAccesses.push(linechartDataArray[i][2]);
    }

    // Generate y values for number of users.
    var yValuesUsers = [];
    for (var i = 0; i < linechartDataArray.length; i++) {
        yValuesUsers.push(linechartDataArray[i][3]);
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

    var data = [trace1, trace2, trace3];

    var layout = {
        title: linechartTitle,
        xaxis: {
            title: linechartColDate,
            tickvals: monthCounter,
            ticktext: monthCounter
        }
    };

    Plotly.newPlot('linechart', data, layout);

}
