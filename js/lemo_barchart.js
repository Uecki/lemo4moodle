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
 * @copyright  2020 Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Language file variables.
var barchartTitle = $('#barchartTitle').val();
var barchartXLabel = $('#barchartXLabel').val();
var barchartYLabel = $('#barchartYLabel').val();

$(document).ready(function() {

    // Download button for barchart tab.
    $('#html_btn_1').click(function() {
        // Opens dialog box.
        $( "#dialog" ).dialog("open");
    });

    // Redraw charts when page is resized.
    $(window).resize(function() {
        block_lemo4moodle_drawBarchart();
    });

    // Minimalize tabs are being initialized, callback function
    // 'block_lemo4moodle_drawBarchart' is executed on tab change.
    $('#tabs').tabs({ 'onShow': block_lemo4moodle_drawBarchart });

});

/**
 * Callback function that draws the barchart.
 * See google charts documentation for barchart: https://developers.google.com/chart/interactive/docs/gallery/barchart.
 * @method block_lemo4moodle_drawBarchart
 */
function block_lemo4moodle_drawBarchart() {
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

    // Check, if the file info is available.
    // Necessary for downloaded file, where it is not available.
    if ($('#barchartFileInfo').length > 0) {

        var barchartDataArray = JSON.parse($('#barchartFileInfo').val());

        // Add event listener that checks, which bar was clicked and then
        // opens the corresponding file.
        google.visualization.events.addListener(materialBarchart, 'select', function() {
            if (typeof materialBarchart.getSelection()[0] !== 'undefined') {
                var selection = data.getValue(materialBarchart.getSelection()[0].row, 0);
                if (selection.length) {
                    barchartDataArray.forEach( function(item) {
                        if (selection == item[0]) {
                            var url = $('#wwwroot').val() + '/pluginfile.php/' + item[1] + '/' + item[2] + '/' + item[3] + '/' + item[4] + '/' + item[5];
                            window.open(url);
                            materialBarchart.setSelection([]);
                            return;
                        }
                    });
                }
            }
        });
    }

}
