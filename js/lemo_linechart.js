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
        var data = new google.visualization.DataTable();
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
            var data = new google.visualization.DataTable();
                data.addColumn('date', linechartColDate);
                data.addColumn('number', linechartColAccess);
                data.addColumn('number', linechartColOwnAccess);
                data.addColumn('number', linechartColUser);
                data.addRows(chartData);
            var options = {
                chart: {
                    title: linechartTitle
                },
                hAxis: {
                    title: linechartColDate,
                    format:'d.M.yy'
                }
            };

            var activitychart = new google.visualization.LineChart(document.getElementById('linechart'));
            activitychart.draw(data, options);
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
 * Callback function that draws the linehchart.
 * See google charts documentation for linechart: https://developers.google.com/chart/interactive/docs/gallery/linechart
 * @method block_lemo4moodle_drawLinechart
 */
function block_lemo4moodle_drawLinechart() {

    var data = new google.visualization.DataTable();
        data.addColumn('date', linechartColDate);
        data.addColumn('number', linechartColAccess);
        data.addColumn('number', linechartColOwnAccess);
        data.addColumn('number', linechartColUser);
        data.addRows(linechartDataArray);
    var options = {
        chart: {
            title: linechartTitle
        },
        hAxis: {
            title: linechartColDate,
            format:'d.M.yy'
        },

    };
    var activitychart = new google.visualization.LineChart(document.getElementById('linechart'));
    activitychart.draw(data, options);

}
