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
 * JS file for everything that can be seen on or is related to the treemap tab.
 *
 * The languae strings used here are initialised as variables in index.php.
 *
 * @package    block_lemo4moodle
 * @copyright  2020 Margarita Elkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Language file variables.
//var treemapData = $('#treemapData').val();
var treemapTitle = $('#treemapTitle').val();
var treemapClickCount = $('#treemapClickCount').val();

$(document).ready(function() {

    // Treemap - reset button. Not yet implemented.
    $('#rst_btn_4').click(function() {
        // Do something.
    });

    // Download button for treemap tab.
    $('#html_btn_4').click(function() {
        // Opens dialog box.
        $( "#dialog" ).dialog( "open" );
    });

    // Redraw charts when page is resized.
    $(window).resize(function() {
        block_lemo4moodle_drawTreemap();
    });

    // Minimalize tabs are being initialized, callback function
    // 'block_lemo4moodle_drawTreemap' is executed on tab change.
    $('#tabs').tabs({ 'onShow': block_lemo4moodle_drawTreemap });

});

/**
 * Callback function that draws the treemap.
 * See google charts documentation for treemap: https://developers.google.com/chart/interactive/docs/gallery/treemap
 */
function block_lemo4moodle_drawTreemap() {

    var data = new google.visualization.arrayToDataTable(treemapData);

    var tree = new google.visualization.TreeMap(document.getElementById('treemap'));

    tree.draw(data, {
        minColor: '#f00',
        midColor: '#ddd',
        maxColor: '#0d0',
        headerHeight: 15,
        fontColor: 'black',
        highlightOnMouseOver: true,
        title: treemapTitle,
        generateTooltip: block_lemo4moodle_showTooltipTreemap
    });

    /**
     * Function to generate a tooltip for tze treemap.
     * For further documentation see google charts documentation for treemap.
     *
     * @param int $row
     * @param int $size
     * @param int $value
     * @return string
     */
    function block_lemo4moodle_showTooltipTreemap(row, size, value) {
        return '<div style="background:#fd9; padding:10px; border-style:solid">' +
            data.getValue(row, 0) + '<br>' + treemapClickCount + size + ' </div>';
    }
}
