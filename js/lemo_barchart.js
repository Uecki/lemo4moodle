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
 * @copyright  2020 Finn Ueckert
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$(document).ready(function() {

    // Barchart - reset button. Not yet implemented.
    $('#rst_btn_1').click(function() {
        // Do something.
    });

    // Download button for barchart tab.
    $('#html_btn_1').click(function() {
        // Opens dialog box.
        $( "#dialog" ).dialog("open");
    });

});

/**
 * Callback function that draws the barchart.
 * See google charts documentation for barchart: https://developers.google.com/chart/interactive/docs/gallery/barchart.
 */
function block_lemo4moodle_draw_barchart() {
    var data = google.visualization.arrayToDataTable(barchartData);

    var materialOptionsBarchart = {
        chart: {
            title: barchartTitle
        },
        axes: {
            x: {
                distance: {label: barchartXLabel} // Bottom x-axis.
            },
            y: {
                distance: {label: barchartYLabel} // Left y-axis.
            }
        },
        legend: {
            position: 'none'

        },
        bars: 'horizontal'
    };

    // Instantiate and draw the bar chart.
    var materialBarchart = new google.charts.Bar(document.getElementById('barchart'));
    materialBarchart.draw(data, google.charts.Bar.convertOptions(materialOptionsBarchart));

}
