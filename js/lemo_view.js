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
 * JS file for all the general functionalities of this block.
 * It is important to note, that this file needs to to be included after the
 * files lemo_linechart, lemo_barchart and lemo_heatmap.js, else it will not work.
 *
 * The languae strings used here are initialised as variables in index.php.
 * Inluded are:
 * dialog box after click on "Download",
 * merging files functionality,
 * function to draw all charts (combines functions of each charts JS file).
 *
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Finn Ueckert, Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Language file variables.
var viewDialogThis = $('#viewDialogThis').val();
var viewDialogAll = $('#viewDialogAll').val();
var viewFile = $('#viewFile').val();
var viewTimespan = $('#viewTimespan').val();
var viewNoTimespan = $('#viewNoTimespan').val();
var viewModalError = $('#viewModalError').val();

$(document).ready(function() {

    block_lemo4moodle_drawAllCharts();

    // Redraw charts when page is resized.
    $(window).resize(function() {
        block_lemo4moodle_drawAllCharts();
    });

    // Closes the window when the button "Schließen" is clicked.
    $('#btn_close').click(function() {
        window.close();
    });

    // Minimalize tabs are being initialized, callback function
    // 'block_lemo4moodle_drawAllCharts' is executed on tab change.
    $('#tabs').tabs({ 'onShow': block_lemo4moodle_drawAllCharts });

    // Initializing the dialog box shown before the download.
    $( "#dialog" ).dialog({
        autoOpen: false,
        minWidth: 500,
        minHeight: 400,
        buttons: [
        {
            text: viewDialogThis,
            click: function() {
                $(this).dialog("close");
                if ($(".active").attr('id') == 'tab1') {
                    document.getElementById("allCharts1").value = 'false';
                    document.getElementById("download_form_1").submit();
                } else if ($(".active").attr('id') == 'tab2') {
                    document.getElementById("allCharts2").value = 'false';
                    document.getElementById("download_form_2").submit();
                } else if ($(".active").attr('id') == 'tab3') {
                    document.getElementById("allCharts3").value = 'false';
                    document.getElementById("download_form_3").submit();
                } else if ($(".active").attr('id') == 'tab4') {
                    document.getElementById("allCharts4").value = 'false';
                    document.getElementById("download_form_4").submit();
                }
            }
        },
        {
            text: viewDialogAll,
            click: function() {
                $(this).dialog("close");
                if ($(".active").attr('id') == 'tab1') {
                    document.getElementById("allCharts1").value = 'true';
                    document.getElementById("download_form_1").submit();
                } else if ($(".active").attr('id') == 'tab2') {
                    document.getElementById("allCharts2").value = 'true';
                    document.getElementById("download_form_2").submit();
                } else if ($(".active").attr('id') == 'tab3') {
                    document.getElementById("allCharts3").value = 'true';
                    document.getElementById("download_form_3").submit();
                } else if ($(".active").attr('id') == 'tab4') {
                    document.getElementById("allCharts4").value = 'true';
                    document.getElementById("download_form_4").submit();
                }
            }
        }
        ]
    });

    // Empty the selected files on load.
    $('#file_merge').val('');

    // Display filenames of selected files.
    $('#file_merge').change(function() {

        // Variables for html elements.
        var input = document.getElementById('file_merge');

        // Clear previous filename list.
        $('#file_merge_filenames').empty();
        $('#file_merge_timespan').empty();

        // Fill div with timespans.
        for (var i = 0; i < input.files.length; ++i) {

            // Read file to get the timespan of the datasets.
            block_lemo4moodle_readFile(input.files[i], function(e) {

                // Fill div with elements containing the filename.
                var file = this.file;
                $( '#file_merge_filenames' ).append('<li class="black-text">' + viewFile + ': ' +
                    file.name + '</li><br>');

                var fileStringDate = e.target.result;
                if (fileStringDate.includes('var firstdate ')) {
                    var root1 = fileStringDate.indexOf('var firstdate =');
                    var start1 = fileStringDate.indexOf('"', root1);
                    var end1 = fileStringDate.indexOf('"', start1 + 1);
                    var date1 = fileStringDate.substring(start1 + 1, end1);

                    var root2 = fileStringDate.indexOf('var lastdate =');
                    var start2 = fileStringDate.indexOf('"', root2);
                    var end2 = fileStringDate.indexOf('"', start2 + 1);
                    var date2 = fileStringDate.substring(start2 + 1, end2);
                    $( '#file_merge_timespan' ).append('<li class="black-text">' + viewTimespan + date1 +
                        ' - ' + date2 + '</li><br>');
                } else {
                    $( '#file_merge_timespan' ).append('<li class="black-text">' + viewNoTimespan + '</li><br>');
                }
            });
        }
    });

});

// JQuery datepicker funtion (for filter).
$(function() {
    $(".datepick").datepicker({
        dateFormat: 'dd.mm.yy'
    });
});

/**
 * Callback that draws all charts.
 * To be optimized to only load chart for current tab.
 * @method block_lemo4moodle_drawAllCharts
 * @see block_lemo4moodle_drawBarchart()
 * @see block_lemo4moodle_drawLinechart()
 * @see block_lemo4moodle_drawHeatmap()
 * @see block_lemo4moodle_drawTreemap()
 */
function block_lemo4moodle_drawAllCharts() {
    if (typeof block_lemo4moodle_drawBarchart === "function") {
        // The variable barchartData is initialized in index.php.
        block_lemo4moodle_drawBarchart(barchartDataFiltered);
        block_lemo4moodle_initFilterBarchart(barchartData);
    }
    if (typeof block_lemo4moodle_drawLinechart === "function") {
        block_lemo4moodle_drawLinechart(linechartDefaultData);
    }
    if (typeof block_lemo4moodle_drawHeatmap === "function") {
        block_lemo4moodle_drawHeatmap(heatmapDefaultData);
    }
}

/* Function to get the timestamp of a date-string.
 *
 * @method block_lemo4moodle_toTimestamp
 * @param string $strdate Date in string format.
 * @return Date Timestamp of the date.
 */
function block_lemo4moodle_toTimestamp(strdate) {
    var date = Date.parse(strdate);
    return date / 1000;
}

/**
 * Function to get the starting timestamp of a filter from a JQuery datepicker.
 *
 * @method block_lemo4moodle_getStartTimestamp
 * @param string $startDatepicker Value of a JQuery datepicker element.
 * @return Date Timestamp of the date.
 */
function block_lemo4moodle_getStartTimestamp(startDatepicker) {
    // Rewrite date.
    var s = startDatepicker.split('.');
    start = s[1] + '/' + s[0] + '/' + s[2];
    start += ' 00:00:00';
    var date = Date.parse(start);
    return date / 1000;
}

/**
 * Function to get the ending timestamp of a filter from a JQuery datepicker..
 *
 * @method block_lemo4moodle_getEndTimestamp
 * @param string $endDatepicker Value of a jquery datepicker element.
 * @return Date Timestamp of the date.
 */
function block_lemo4moodle_getEndTimestamp(endDatepicker) {
    // Rewrite date.
    var e = endDatepicker.split('.');
    var end = e[1] + '/' + e[0] + '/' + e[2];
    end += ' 23:59:59';
    var date = Date.parse(end);
    return date / 1000;
}

// Initialize the Materialize modal (PopUp) and select.
$(document).ready(function() {
     $('.modal').modal();
     $( "#dialog" ).css('visibility', 'visible');
     $('select').material_select();
});

// Variables for filemerging.
var barchartDataExtracted = [];
var linechartDataExtracted = [];
var heatmapDataExtracted = [];

// Functionality for filemerging.
$('#mergeButton').click(function() {
    $("#modal_error2").text("");
    var filemerge = document.querySelector('#file_merge');

    // Check, if 2 or more files were selected.
    if (filemerge.files.length < 2) {
        $("#modal_error2").text (viewModalError);
        return;
    }

    // Empty merged data variable.
    barchartDataExtracted = [];
    linechartDataExtracted = [];
    heatmapDataExtracted = [];

    var files = filemerge.files;
    var fileString;
    var chartType = ["linechartData"];
    //var chartType = ["barchartData", "linechartData", "heatmapData"];

    // Variable to keep track of loop (for callback).
    var loop = 0;

    // Iterate trough each selected file.
    for (var i = 0; i < files.length; i++) {
        // Callback function.
        block_lemo4moodle_readFile(files[i], function(e) {
            fileString = e.target.result;
            // Iterate through each chartType.
            chartType.forEach(function(it) {
                // Get the data from the file as an array of strings.
                var start = fileString.indexOf("[", fileString.indexOf("var " + it + " ="));
                var end = fileString.indexOf(";", fileString.indexOf("var " + it + " ="));
                var rawData = fileString.substring(start, end);
                var data = rawData.substring(2, rawData.lastIndexOf("]]"));
                data = data.replace(/["']/g, "");
                var dataArray;
                if (it == "linechartData") {
                    dataArray = data.split("],[");
                } else if (it == "heatmapData") {
                    dataArray = data.split("],[");
                } else if (it == "barchartData") {

                }

                dataArray.forEach(function(item) {

                    // Collect linechart data.
                    if (it == "linechartData" && item.toString().length > 2) { // Filter out the empty data.

                        // Transform string to array.
                        var splitValues = item.split(",");
                        if (!(linechartDataExtracted).includes(splitValues)) {
                            linechartDataExtracted.push(splitValues);
                        }
                    }
                });

                //Check, if all files were iterated.
                if (chartType[chartType.length - 1] == it && loop == (files.length - 1)) {

                    // Create array with all the merged datasets of each graph.
                    var allDataArray = [];

                    // Sort array.
                    linechartDataExtracted.sort(function(a,b){
                    // Turn your strings into dates, and then subtract them
                    // to get a value that is either negative, positive, or zero.
                    return new Date(b.date) - new Date(a.date);
                    });

                    allDataArray.push(linechartDataExtracted);
                    allDataArray.push(barchartDataExtracted);
                    allDataArray.push(heatmapDataExtracted);
                    var jsonArray = JSON.stringify(allDataArray);
                    console.log(jsonArray);
                    // Reset the data variable.
                    $("#allCharts2").val('false');
                    $("#mergeData2").val(jsonArray);
                    $("#download_form_2").submit();
                    $("#mergeData2").val('');
                }
            });
            loop++;
        });
    }
});

/**
 * Callback function for the FileReader. Reads file given as parameter.
 *
 * @method block_lemo4moodle_readFile
 * @param File   $file The file to be read.
 * @param function $onloadcallback Function that is to be executed on load.
 */
function block_lemo4moodle_readFile(file, onloadcallback) {
    var reader = new FileReader();
    reader.file = file;
    reader.onload = onloadcallback;
    reader.readAsText(file);
}
