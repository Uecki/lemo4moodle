/*JS-file for everything that can be seen on or is related to the treemap-tab.  Uses language-strings initialised in index.php.*/

$(document).ready(function() {

	// Treemap - reset button
	$('#rst_btn_4').click(function() {
	/* do something */
	});

	//Download button for treemap tab.
	$('#html_btn_4').click(function() {
		//Opens dialog box.
		$( "#dialog" ).dialog( "open" );
	});

});

//Callback that draws the treemap.
//See google charts  documentation for treemap.
function drawTreeMap() {

	var data = new google.visualization.arrayToDataTable(treemap_data);

	tree = new google.visualization.TreeMap(document.getElementById('treemap'));

	tree.draw(data, {
		minColor: '#f00',
		midColor: '#ddd',
		maxColor: '#0d0',
		headerHeight: 15,
		fontColor: 'black',
		highlightOnMouseOver: true,
		title: treemap_title,
		generateTooltip: showTooltipTreemap
	});

	function showTooltipTreemap(row, size, value) {
		return '<div style="background:#fd9; padding:10px; border-style:solid">' + treemap_clickCount + size + ' </div>';
	}
}
