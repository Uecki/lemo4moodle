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
 * JS file for everything that can be seen on or is related to the heatmap tab.
 *
 * The languae strings used here are initialised as variables in index.php.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Finn Ueckert, Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Language file variables.
var heatmapTitle = $('#heatmapTitle').val();
var heatmapAll = $('#heatmapAll').val();
var heatmapOwn = $('#heatmapOwn').val();
var heatmapOverall = $('#heatmapOverall').val();
var heatmapAverage = $('#heatmapAverage').val();
var heatmapMonday = $('#heatmapMonday').val();
var heatmapTuesday = $('#heatmapTuesday').val();
var heatmapWednesday = $('#heatmapWednesday').val();
var heatmapThursday = $('#heatmapThursday').val();
var heatmapFriday = $('#heatmapFriday').val();
var heatmapSaturday = $('#heatmapSaturday').val();
var heatmapSunday = $('#heatmapSunday').val();
var heatmapCheckSelection = $('#heatmapCheckSelection').val();
var heatmapDefaultData = block_lemo4moodle_createHeatmapData(heatmapData);
var filteredHeatmapData = heatmapDefaultData;

$(document).ready(function() {

    // Heatmap - reset button.
    $('#rst_btn_3').click(function() {
        block_lemo4moodle_drawHeatmap(heatmapDefaultData);
        flteredHeatmapData = heatmapDefaultData;
        $('#datepicker_5').val("");
        $('#datepicker_6').val("");

    });

    // Filter for Heatmap.
    $('#dp_button_3').click(function() {

        var startTimestamp = block_lemo4moodle_getStartTimestamp(document.getElementById('datepicker_5').value);
        var endTimestamp = block_lemo4moodle_getEndTimestamp(document.getElementById('datepicker_6').value);
        if (startTimestamp <= endTimestamp) {

            filteredHeatmapData = block_lemo4moodle_createHeatmapData(heatmapData, startTimestamp, endTimestamp);
            block_lemo4moodle_drawHeatmap(filteredHeatmapData);

        } else {
            Materialize.toast(viewCheckSelection, 3000); // 3000 is the duration of the toast.
            $('#datepicker_5').val("");
            $('#datepicker_6').val("");
        }

    });

    // Download button for heatmap tab.
    $('#html_btn_3').click(function() {
        // Opens dialog box.
        $( "#dialog" ).dialog( "open" );
    });

    // Redraw charts when page is resized.
    $(window).resize(function() {
        block_lemo4moodle_drawHeatmap(heatmapDefaultData);
    });

    // Minimalize tabs are being initialized, callback function
    // 'block_lemo4moodle_drawHeatmap' is executed on tab change.
    $('#tabs').tabs({ 'onShow': block_lemo4moodle_drawHeatmap(heatmapDefaultData) });
});

/**
 * Function that rearranges and processes the query result to fit into the right format for plotly.
 * See plotly documentation for heatmap: https://plot.ly/javascript/heatmaps/
 *
 * @method block_lemo4moodle_createHeatmapData
 * @param Object queryResult Query result fetched from the server and given to js in index.php.
 * @param date startTimestamp Optional parameter, used as starting date if the data is to be filtered.
 * @param date endTimestamp Optional parameter, used as ending date if the data is to be filtered.
 */
function block_lemo4moodle_createHeatmapData(queryResult, startTimestamp = 0, endTimestamp = 0) {
    // Create heatmap data.
    var timespan;
    var filteredHeatmapData = [];
    var counterWeekdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

    // Associative array (object) for total number of weekday actions.
    var totalHits = {
        "Monday"  : 0,
        "Tuesday"  : 0,
        "Wednesday"  : 0,
        "Thursday"  : 0,
        "Friday"  : 0,
        "Saturday"  : 0,
        "Sunday"  : 0
    };

    // Associative array (object) for total  number of own weekday actions.
    var totalOwnHits = {
        "Monday"  : 0,
        "Tuesday"  : 0,
        "Wednesday"  : 0,
        "Thursday"  : 0,
        "Friday"  : 0,
        "Saturday"  : 0,
        "Sunday"  : 0
    };

    // Associative array (object) to assign the query results.
    var weekdays = {
        "Monday" : {
            "0to6" : {
                "all" : {
                    "col"  : 0,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 1,
                    "value" : 0,
                },
            },
            "6to12" : {
                "all" : {
                    "col"  : 2,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 3,
                    "value" : 0,
                },
            },

            "12to18" : {
                "all" : {
                    "col"  : 4,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 5,
                    "value" : 0,
                },
            },

            "18to24" : {
                "all" : {
                    "col"  : 6,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 7,
                    "value" : 0,
                },
            },
            "row" : 0,
        },
        "Tuesday" : {
            "0to6" : {
                "all" : {
                    "col"  : 0,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 1,
                    "value" : 0,
                },
            },
            "6to12" : {
                "all" : {
                    "col"  : 2,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 3,
                    "value" : 0,
                },
            },

            "12to18" : {
                "all" : {
                    "col"  : 4,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 5,
                    "value" : 0,
                },
            },

            "18to24" : {
                "all" : {
                    "col"  : 6,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 7,
                    "value" : 0,
                },
            },
            "row" : 1,
        },
        "Wednesday" : {
            "0to6" : {
                "all" : {
                    "col"  : 0,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 1,
                    "value" : 0,
                },
            },
            "6to12" : {
                "all" : {
                    "col"  : 2,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 3,
                    "value" : 0,
                },
            },

            "12to18" : {
                "all" : {
                    "col"  : 4,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 5,
                    "value" : 0,
                },
            },

            "18to24" : {
                "all" : {
                    "col"  : 6,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 7,
                    "value" : 0,
                },
            },
            "row" : 2,
        },
        "Thursday" : {
            "0to6" : {
                "all" : {
                    "col"  : 0,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 1,
                    "value" : 0,
                },
            },
            "6to12" : {
                "all" : {
                    "col"  : 2,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 3,
                    "value" : 0,
                },
            },

            "12to18" : {
                "all" : {
                    "col"  : 4,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 5,
                    "value" : 0,
                },
            },

            "18to24" : {
                "all" : {
                    "col"  : 6,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 7,
                    "value" : 0,
                },
            },
            "row" : 3,
        },
        "Friday" : {
            "0to6" : {
                "all" : {
                    "col"  : 0,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 1,
                    "value" : 0,
                },
            },
            "6to12" : {
                "all" : {
                    "col"  : 2,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 3,
                    "value" : 0,
                },
            },

            "12to18" : {
                "all" : {
                    "col"  : 4,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 5,
                    "value" : 0,
                },
            },

            "18to24" : {
                "all" : {
                    "col"  : 6,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 7,
                    "value" : 0,
                },
            },
            "row" : 4,
        },
        "Saturday" : {
            "0to6" : {
                "all" : {
                    "col"  : 0,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 1,
                    "value" : 0,
                },
            },
            "6to12" : {
                "all" : {
                    "col"  : 2,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 3,
                    "value" : 0,
                },
            },

            "12to18" : {
                "all" : {
                    "col"  : 4,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 5,
                    "value" : 0,
                },
            },

            "18to24" : {
                "all" : {
                    "col"  : 6,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 7,
                    "value" : 0,
                },
            },
            "row" : 5,
        },
        "Sunday" : {
            "0to6" : {
                "all" : {
                    "col"  : 0,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 1,
                    "value" : 0,
                },
            },
            "6to12" : {
                "all" : {
                    "col"  : 2,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 3,
                    "value" : 0,
                },
            },

            "12to18" : {
                "all" : {
                    "col"  : 4,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 5,
                    "value" : 0,
                },
            },

            "18to24" : {
                "all" : {
                    "col"  : 6,
                    "value" : 0,
                },
                "own" : {
                    "col"  : 7,
                    "value" : 0,
                },
            },
            "row" : 6,
        },
    };

    // Iterate through each element of the original query.
    queryResult.forEach(function(item) {

        // indices for item: 0->timecreated, 1->weekday, 2->hour, 3->allhits, 4->ownhits
        // Check if the function is called by the filter or for the default graph.
        if(startTimestamp == 0 && endTimestamp == 0) {
            // Link timespan to column in heatmap.
            if (parseInt(item.hour) >= 0  && parseInt(item.hour) < 6) {
                timespan = "0to6";
            } else if (parseInt(item.hour) >= 6  && parseInt(item.hour) < 12) {
                timespan = "6to12";
            } else if (parseInt(item.hour) >= 12  && parseInt(item.hour) < 18) {
                timespan = "12to18";
            } else if (parseInt(item.hour) >= 18  && parseInt(item.hour) < 24) {
                timespan = "18to24";
            }

            // Data for specific day.
            weekdays[item.weekday][timespan]["all"]["value"] += parseInt(item.allhits);
            weekdays[item.weekday][timespan]["own"]["value"] += parseInt(item.ownhits);

            // Data for overall clicks.
            totalHits[item.weekday] += parseInt(item.allhits);
            totalOwnHits[item.weekday] += parseInt(item.ownhits);
        }
        else {
            // Check, if the timestamp is included in the filter.
            if (item.timecreated >= startTimestamp && item.timecreated <= endTimestamp) {

                // Link timespan to column in heatmap.
                if (parseInt(item.hour) >= 0  && parseInt(item.hour) < 6) {
                    timespan = "0to6";
                } else if (parseInt(item.hour) >= 6  && parseInt(item.hour) < 12) {
                    timespan = "6to12";
                } else if (parseInt(item.hour) >= 12  && parseInt(item.hour) < 18) {
                    timespan = "12to18";
                } else if (parseInt(item.hour) >= 18  && parseInt(item.hour) < 24) {
                    timespan = "18to24";
                }

                // Data for specific day.
                weekdays[item.weekday][timespan]["all"]["value"] += parseInt(item.allhits);
                weekdays[item.weekday][timespan]["own"]["value"] += parseInt(item.ownhits);

                // Data for overall clicks.
                totalHits[item.weekday] += parseInt(item.allhits);
                totalOwnHits[item.weekday] += parseInt(item.ownhits);
            }
        }
    });

    // Put data of each weekdayfield into suitable format for the chart.
    var counter = 0;
    while (counter <= 6) {

        filteredHeatmapData.push([weekdays[counterWeekdays[counter]]['0to6']['all']['value'],
            weekdays[counterWeekdays[counter]]['0to6']['own']['value'],
            weekdays[counterWeekdays[counter]]['6to12']['all']['value'],
            weekdays[counterWeekdays[counter]]['6to12']['own']['value'],
            weekdays[counterWeekdays[counter]]['12to18']['all']['value'],
            weekdays[counterWeekdays[counter]]['12to18']['own']['value'],
            weekdays[counterWeekdays[counter]]['18to24']['all']['value'],
            weekdays[counterWeekdays[counter]]['18to24']['own']['value'],
            totalHits[counterWeekdays[counter]],
            totalOwnHits[counterWeekdays[counter]],
            Math.round(totalHits[counterWeekdays[counter]] / 4.0),
            Math.round(totalOwnHits[counterWeekdays[counter]] / 4.0)]);

        counter = counter + 1;
    }
    return filteredHeatmapData;
}


/**
 * Callback function that draws the heatmap.
 * See plotly documentation for heatmap: https://plot.ly/javascript/heatmaps/
 * @method block_lemo4moodle_drawHeatmap
 */
function block_lemo4moodle_drawHeatmap(data) {
    var xValues = [heatmapAll + '<br>00:00-06:00', heatmapOwn + '<br>00:00-06:00', heatmapAll +
        '<br>06:00-12:00', heatmapOwn + '<br>06:00-12:00', heatmapAll + '<br>12:00-18:00', heatmapOwn +
        '<br>12:00-18:00', heatmapAll + '<br>18:00-24:00', heatmapOwn + '<br>18:00-24:00',  heatmapAll +
        '<br>' + heatmapOverall, heatmapOwn + '<br>' + heatmapOverall, heatmapAll + '<br>' +
        heatmapAverage, heatmapOwn + '<br>' + heatmapAverage];

    var yValues = [heatmapMonday, heatmapTuesday, heatmapWednesday, heatmapThursday, heatmapFriday,
            heatmapSaturday, heatmapSunday];

    var zValues = data;

    var colorscaleValue = [
        [0, '#ffffff'],
        [1, '#4a9dd4']
    ];

    var completeData = [{
        x: xValues,
        y: yValues,
        z: zValues,
        type: 'heatmap',
        colorscale: colorscaleValue,
        showscale: false
    }];

    var layout = {
        title: heatmapTitle,
        annotations: [],
        xaxis: {
            ticks: '',
            side: 'top'
        },
        yaxis: {
            ticks: '',
            ticksuffix: ' ',
            width: 700,
            height: 700,
            autosize: false
        }
    };

    for (var i = 0; i < yValues.length; i++) {
        for (var j = 0; j < xValues.length; j++) {
            var currentValue = zValues[i][j];
            if (currentValue != 0.0) {
                var textColor = 'black';
            } else {
                var textColor = 'black';
            }
            var result = {
                xref: 'x1',
                yref: 'y1',
                x: xValues[j],
                y: yValues[i],
                text: zValues[i][j],
                font: {
                    family: 'Arial',
                    size: 12,
                    color: textColor
                },
                showarrow: false,
            };
            layout.annotations.push(result);
        }
    }

    Plotly.newPlot('heatmap', completeData, layout);
}
