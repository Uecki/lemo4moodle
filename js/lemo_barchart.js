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
var barchartTitle = $('#barchartTitle').val();
var barchartXLabel = $('#barchartXLabel').val();
var barchartYLabel = $('#barchartYLabel').val();
var barchartUser = $('#linechartColUser').val();

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

// Generate x values for clicks.
    var xValuesClicks = [];
	counter = barchartData.length - 1;
    while (counter >= 1) {
		xValuesClicks.push(
            barchartData[counter][1]
		)
		counter-- ;
    }

    // Generate x value for users.
	var xValuesUser = [];
	var counter = barchartData.length - 1;
    while (counter >= 1) {
		xValuesUser.push(
            barchartData[counter][2]
		)
		counter-- ;
    }

    // Generate y value.
    var yValues = [];
    counter = barchartData.length - 1;
    while (counter >= 1) {
        yValues.push(
            barchartData[counter][0]
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

	var data = [trace1, trace2];

	var layout = {
        title: barchartTitle,
        barmode: 'group',
        margin: {
            l: 150,
            r: 20,
            t: 200,
            b: 70
        }
	};


	Plotly.newPlot('barchart', data, layout);

    // Check, if the file info is available.
    // Necessary for downloaded file, where it is not available.
    if ($('#barchartFileInfo').length > 0) {

        var barchartDataArray = JSON.parse($('#barchartFileInfo').val());
        var plotlyBarchart = document.getElementById('barchart');
        plotlyBarchart.on('plotly_click', function(data) {
            var clickVal = data.points[0].y;
            barchartDataArray.forEach( function(item) {
                if (clickVal == item[0]) {
                    var url = $('#wwwroot').val() + '/pluginfile.php/' + item[1] + '/' + item[2] + '/' + item[3] + '/' + item[4] + '/' + item[5];
                    window.open(url);
                    return;
                }
            });
        });
    }
}
