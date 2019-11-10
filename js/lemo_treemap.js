/*JS-file for everything that can be seen on or is related to the treemap-tab.*/

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
	  title: 'TreeMap für die Anzahl der Klicks pro Datei. Rechtsklick, um eine Ebene nach oben zu gelangen.',
	  generateTooltip: showTooltipTreemap
	});
	
	function showTooltipTreemap(row, size, value) {
		return '<div style="background:#fd9; padding:10px; border-style:solid">' + ' Anzahl der Klicks: ' + size + ' </div>';
	}
}