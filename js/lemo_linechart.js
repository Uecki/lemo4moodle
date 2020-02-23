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
 * @copyright  2020 Finn Ueckert
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$(document).ready(function() {

    // Line Chart - reset button.
    $('#rst_btn_2').click(function() {
        var data = new google.visualization.DataTable();
        block_lemo4moodle_draw_linechart();
        $("#datepicker_3").val("");
        $("#datepicker_4").val("");
    });

    // Filter for Linechart.
    $('#dp_button_2').click(function() {
        var start = document.getElementById('datepicker_3').value;
        var end = document.getElementById('datepicker_4').value;
        // Rewrite date.
        s = start.split('.');
        start = s[1] + '/' + s[0] + '/' + s[2];
        // Rewrite date.
        e = end.split('.');
        end = e[1] + '/' + e[0] + '/' + e[2];
        start += ' 00:00:00';
        end += ' 23:59:59';
        var starttimestamp = block_lemo4moodle_to_timestamp(start);
        var endtimestamp = block_lemo4moodle_to_timestamp(end);
        if (starttimestamp <= endtimestamp) {
            var activitydata = [];
            linechartdataarrayfilter.forEach(function(item) {
                if (item.timestamp >= starttimestamp && item.timestamp <= endtimestamp) {
                    activitydata.push({
                        date: item.date,
                        accesses: item.accesses,
                        ownhits: item.ownhits,
                        users: item.user
                    });
                }
            });
            var chartdata = activitydata.map(function(it) {
                var str = it.date;
                r = str.split(', ');
                return [new Date(r[0], r[1], r[2]), it.accesses, it.ownhits, it.users];
            });
            var data = new google.visualization.DataTable();
                data.addColumn('date', linechart_colDate);
                data.addColumn('number', linechart_colAccess);
                data.addColumn('number', linechart_colOwnAccess);
                data.addColumn('number', linechart_colUser);
                data.addRows(chartdata);
            var options = {
                chart: {
                    title: linechart_title
                },
                hAxis: {
                    title: linechart_colDate,
                    format:'d.M.yy'
                }
            };
            activitychart.draw(data, options);
        } else {
            Materialize.toast(linechart_checkSelection, 3000) // 3000 is the duration of the toast.
            $('#datepicker_3').val("");
            $('#datepicker_4').val("");
        }
    });

    // Download button for linechart tab.
    $('#html_btn_2').click(function() {
        // Opens dialog box.
        $( "#dialog" ).dialog( "open" );
    });

});

/**
 * Callback function that draws the linehchart.
 * See google charts documentation for linechart: https://developers.google.com/chart/interactive/docs/gallery/linechart
 */
function block_lemo4moodle_draw_linechart() {

    var data = new google.visualization.DataTable();
        data.addColumn('date', linechart_colDate);
        data.addColumn('number', linechart_colAccess);
        data.addColumn('number', linechart_colOwnAccess);
        data.addColumn('number', linechart_colUser);
        data.addRows(linechartdataarray);
    var options = {
        chart: {
            title: linechart_title
        },
        hAxis: {
            title: linechart_colDate,
            format:'d.M.yy'
        },

    };

    activitychart = new google.visualization.LineChart(document.getElementById('linechart'));
    activitychart.draw(data, options);

}
