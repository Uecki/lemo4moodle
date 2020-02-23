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
 * @copyright  2020 Finn Ueckert
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

});

/**
 * Callback function that draws the treemap.
 * See google charts documentation for treemap: https://developers.google.com/chart/interactive/docs/gallery/treemap
 */
function block_lemo4moodle_draw_treemap() {

    var data = new google.visualization.arrayToDataTable(treemapdata);

    tree = new google.visualization.TreeMap(document.getElementById('treemap'));

    tree.draw(data, {
        minColor: '#f00',
        midColor: '#ddd',
        maxColor: '#0d0',
        headerHeight: 15,
        fontColor: 'black',
        highlightOnMouseOver: true,
        title: treemap_title,
        generateTooltip: block_lemo4moodle_show_tooltip_treemap
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
    function block_lemo4moodle_show_tooltip_treemap(row, size, value) {
        return '<div style="background:#fd9; padding:10px; border-style:solid">' + data.getValue(row, 0) + '<br>' + treemap_clickCount + size + ' </div>';
    }
}
