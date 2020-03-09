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
 * @copyright  2020 Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

variables
// Language file variables.
var heatmapData = $('#heatmapData').value;
var heatmapDataFilter = $('#heatmapDataFilter').value;
var heatmapTitle = $('#heatmapTitle').value;
var heatmapAll = $('#heatmapAll').value;
var heatmapOwn = $('#heatmapOwn').value;
var heatmapOverall = $('#heatmapOverall').value;
var heatmapAverage = $('#heatmapAverage').value;
var heatmapMonday = $('#heatmapMonday').value;
var heatmapTuesday = $('#heatmapTuesday').value;
var heatmapWednesday = $('#heatmapWednesday').value;
var heatmapThursday = $('#heatmapThursday').value;
var heatmapFriday = $('#heatmapFriday').value;
var heatmapSaturday = $('#heatmapSaturday').value;
var heatmapSunday = $('#heatmapSunday').value;
var heatmapCheckSelection = $('#heatmapCheckSelection').value;

$(document).ready(function() {

    // Heatmap - reset button.
    $('#rst_btn_3').click(function() {
        block_lemo4moodle_drawHeatmap();
        $('#datepicker_5').val("");
        $('#datepicker_6').val("");

    });

    // Filter for Heatmap.
    $('#dp_button_3').click(function() {

        var start = document.getElementById('datepicker_5').value;
        var end = document.getElementById('datepicker_6').value;
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
            heatmapDataFilter.forEach(function(item) {

                // Check, if the timestamp is included in the filter.
                if (item[1].timecreated >= startTimestamp && item[1].timecreated <= endTimestamp) {

                    // Link timespan to column in heatmap.
                    if (parseInt(item[1].hour) >= 0  && parseInt(item[1].hour) < 6) {
                        timespan = "0to6";
                    } else if (parseInt(item[1].hour) >= 6  && parseInt(item[1].hour) < 12) {
                        timespan = "6to12";
                    } else if (parseInt(item[1].hour) >= 12  && parseInt(item[1].hour) < 18) {
                        timespan = "12to18";
                    } else if (parseInt(item[1].hour) >= 18  && parseInt(item[1].hour) < 24) {
                        timespan = "18to24";
                    }

                    // Data for specific day.
                    weekdays[item[1].weekday][timespan]["all"]["value"] += parseInt(item[1].allhits);
                    weekdays[item[1].weekday][timespan]["own"]["value"] += parseInt(item[1].ownhits);

                    // Data for overall clicks.
                    totalHits[item[1].weekday] += parseInt(item[1].allhits);
                    totalOwnHits[item[1].weekday] += parseInt(item[1].ownhits);
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

            var xValues = [heatmapAll + '<br>00:00-06:00', heatmapOwn + '<br>00:00-06:00', heatmapAll +
                '<br>06:00-12:00', heatmapOwn + '<br>06:00-12:00', heatmapAll + '<br>12:00-18:00', heatmapOwn +
                '<br>12:00-18:00', heatmapAll + '<br>18:00-24:00', heatmapOwn + '<br>18:00-24:00',  heatmapAll +
                '<br>' + heatmapOverall, heatmapOwn + '<br>' + heatmapOverall, heatmapAll + '<br>' +
                heatmapAverage, heatmapOwn + '<br>' + heatmapAverage];

            var yValues = [heatmapMonday, heatmapTuesday, heatmapWednesday, heatmapThursday, heatmapFriday,
                    heatmapSaturday, heatmapSunday];

            var zValues = filteredHeatmapData;

            var colorscaleValue = [
                [0, '#3D9970'],
                [1, '#001f3f']
            ];

            var data = [{
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

            for ( var i = 0; i < yValues.length; i++ ) {
                for ( var j = 0; j < xValues.length; j++ ) {
                    var currentValue = zValues[i][j];
                    if (currentValue != 0.0) {
                        var textColor = 'white';
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

            Plotly.newPlot('heatmap', data, layout);

        } else {
            Materialize.toast(heatmapCheckSelection, 3000); // 3000 is the duration of the toast.
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
        block_lemo4moodle_drawHeatmap();
    });

    // Minimalize tabs are being initialized, callback function
    // 'block_lemo4moodle_drawHeatmap' is executed on tab change.
    $('#tabs').tabs({ 'onShow': block_lemo4moodle_drawHeatmap });
});

/**
 * Callback function that draws the heatmap.
 * See highcharts documentation for heatmap: https://www.highcharts.com/demo/heatmap
 */
function block_lemo4moodle_drawHeatmap() {
    var xValues = [heatmapAll + '<br>00:00-06:00', heatmapOwn + '<br>00:00-06:00', heatmapAll +
        '<br>06:00-12:00', heatmapOwn + '<br>06:00-12:00', heatmapAll + '<br>12:00-18:00', heatmapOwn +
        '<br>12:00-18:00', heatmapAll + '<br>18:00-24:00', heatmapOwn + '<br>18:00-24:00',  heatmapAll +
        '<br>' + heatmapOverall, heatmapOwn + '<br>' + heatmapOverall, heatmapAll + '<br>' +
        heatmapAverage, heatmapOwn + '<br>' + heatmapAverage];

    var yValues = [heatmapMonday, heatmapTuesday, heatmapWednesday, heatmapThursday, heatmapFriday,
            heatmapSaturday, heatmapSunday];

    var zValues = heatmapData;

    var colorscaleValue = [
        [0, '#3D9970'],
        [1, '#001f3f']
    ];

    var data = [{
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

    for ( var i = 0; i < yValues.length; i++ ) {
        for ( var j = 0; j < xValues.length; j++ ) {
            var currentValue = zValues[i][j];
            if (currentValue != 0.0) {
                var textColor = 'white';
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

    Plotly.newPlot('heatmap', data, layout);
}
