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
 * @copyright  2020 Finn Ueckert
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$(document).ready(function() {

    // Heatmap - reset button.
    $('#rst_btn_3').click(function() {
        block_lemo4moodle_draw_heatmap();
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
        var startTimestamp = block_lemo4moodle_to_timestamp(start);
        var endTimestamp = block_lemo4moodle_to_timestamp(end);
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

                filteredHeatmapData.push([weekdays[counterWeekdays[counter]]['0to6']['all']['col'],weekdays[counterWeekdays[counter]]['row'],weekdays[counterWeekdays[counter]]['0to6']['all']['value']]);

                filteredHeatmapData.push([weekdays[counterWeekdays[counter]]['0to6']['own']['col'],weekdays[counterWeekdays[counter]]['row'],weekdays[counterWeekdays[counter]]['0to6']['own']['value']]);

                filteredHeatmapData.push([weekdays[counterWeekdays[counter]]['6to12']['all']['col'],weekdays[counterWeekdays[counter]]['row'],weekdays[counterWeekdays[counter]]['6to12']['all']['value']]);

                filteredHeatmapData.push([weekdays[counterWeekdays[counter]]['6to12']['own']['col'],weekdays[counterWeekdays[counter]]['row'],weekdays[counterWeekdays[counter]]['6to12']['own']['value']]);

                filteredHeatmapData.push([weekdays[counterWeekdays[counter]]['12to18']['all']['col'],weekdays[counterWeekdays[counter]]['row'],weekdays[counterWeekdays[counter]]['12to18']['all']['value']]);

                filteredHeatmapData.push([weekdays[counterWeekdays[counter]]['12to18']['own']['col'],weekdays[counterWeekdays[counter]]['row'],weekdays[counterWeekdays[counter]]['12to18']['own']['value']]);

                filteredHeatmapData.push([weekdays[counterWeekdays[counter]]['18to24']['all']['col'],weekdays[counterWeekdays[counter]]['row'],weekdays[counterWeekdays[counter]]['18to24']['all']['value']]);

                filteredHeatmapData.push([weekdays[counterWeekdays[counter]]['18to24']['own']['col'],weekdays[counterWeekdays[counter]]['row'],weekdays[counterWeekdays[counter]]['18to24']['own']['value']]);

                counter = counter + 1;
            }

            // Put data of overall clicks into suitable format for the chart.
            var x = 8; // For total and average hits.
            while (x <= 11) {
                var y = 0; // For weekdays.
                while (y <= 6) {
                    if (x == 8) {
                        filteredHeatmapData.push([x, y, totalHits[counterWeekdays[y]]]);
                    } else if (x == 9) {
                        filteredHeatmapData.push([x, y, totalOwnHits[counterWeekdays[y]]]);
                    } else if (x == 10) {
                        filteredHeatmapData.push([x, y, Math.round(totalHits[counterWeekdays[y]] / 7.0)]);
                    } else if (x == 11) {
                        filteredHeatmapData.push([x, y, Math.round(totalOwnHits[counterWeekdays[y]] / 7.0)]);
                    }

                    y  = y + 1;
                }
                x = x + 1;
            }

            // Initialize heatmap chart.
            Highcharts.chart('heatmap', {

                chart: {
                    type: 'heatmap',
                    marginTop: 40,
                    marginBottom: 80,
                    plotBorderWidth: 1
                },

                title: {
                    text: heatmap_title
                },

                xAxis: {
                    categories: [heatmap_all + '<br>00:00-06:00', heatmap_own + '<br>00:00-06:00', heatmap_all + '<br>06:00-12:00', heatmap_own + '<br>06:00-12:00', heatmap_all + '<br>12:00-18:00',
                        heatmap_own + '<br>12:00-18:00', heatmap_all + '<br>18:00-24:00', heatmap_own + '<br>18:00-24:00',  heatmap_all + '<br>' + heatmap_overall, heatmap_own + '<br>' + heatmap_overall,
                        heatmap_all + '<br>' + heatmap_average, heatmap_own + '<br>' + heatmap_average]
                },

                yAxis: {
                    categories: [heatmap_monday, heatmap_tuesday, heatmap_wednesday, heatmap_thursday, heatmap_friday, heatmap_saturday, heatmap_sunday],
                    title: null
                },

                colorAxis: {
                    min: 0,
                    minColor: '#FFFFFF',
                    maxColor: Highcharts.getOptions().colors[0]
                },

                legend: {
                    align: 'right',
                    layout: 'vertical',
                    margin: 0,
                    verticalAlign: 'top',
                    y: 25,
                    symbolHeight: 280
                },

                tooltip: false,

                series: [{
                    name: 'Actions per day',
                    borderWidth: 1,
                    data: filteredHeatmapData, // Convert data string to array.
                    dataLabels: {
                        enabled: true,
                        color: '#000000'
                    }
                }]

            });
        } else {
            Materialize.toast(heatmap_checkSelection, 3000) // 3000 is the duration of the toast.
            $('#datepicker_5').val("");
            $('#datepicker_6').val("");
        }

    });

    // Download button for heatmap tab.
    $('#html_btn_3').click(function() {
        // Opens dialog box.
        $( "#dialog" ).dialog( "open" );
    });
});

/**
 * Callback function that draws the heatmap.
 * See highcharts documentation for heatmap: https://www.highcharts.com/demo/heatmap
 */
function block_lemo4moodle_draw_heatmap() {
    Highcharts.chart('heatmap', {

        chart: {
            type: 'heatmap',
            marginTop: 40,
            marginBottom: 80,
            plotBorderWidth: 1
        },

        title: {
            text: heatmap_title
        },

        xAxis: {
            categories: [heatmap_all + '<br>00:00-06:00', heatmap_own + '<br>00:00-06:00', heatmap_all + '<br>06:00-12:00', heatmap_own + '<br>06:00-12:00', heatmap_all + '<br>12:00-18:00', heatmap_own + '<br>12:00-18:00', heatmap_all + '<br>18:00-24:00', heatmap_own + '<br>18:00-24:00',  heatmap_all + '<br>' + heatmap_overall, heatmap_own + '<br>' + heatmap_overall, heatmap_all + '<br>' + heatmap_average, heatmap_own + '<br>' + heatmap_average]
        },

        yAxis: {
            categories: [heatmap_monday, heatmap_tuesday, heatmap_wednesday, heatmap_thursday, heatmap_friday, heatmap_saturday, heatmap_sunday],
            title: null
        },

        colorAxis: {
            min: 0,
            minColor: '#FFFFFF',
            maxColor: Highcharts.getOptions().colors[0]
        },

        legend: {
            align: 'right',
            layout: 'vertical',
            margin: 0,
            verticalAlign: 'top',
            y: 25,
            symbolHeight: 280
        },

        tooltip: false,

        series: [{
            name: 'Actions per day',
            borderWidth: 1,
            data: heatmapData,
            dataLabels: {
                enabled: true,
                color: '#000000'
            }
        }]

    });
}
