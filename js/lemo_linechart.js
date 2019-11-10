/*JS-file for everything that can be seen on or is related to the linechart-tab.*/

// Variable for ...
var activity_chart;

$(document).ready(function() {
	
	//Line Chart - reset button
	$('#rst_btn_2').click(function() {
		var data = new google.visualization.DataTable();
		drawLineChart();
		$("#datepicker_3").val("");
		$("#datepicker_4").val("");
		
		//var test = "<?php echo $allData ?>";
		//console.log(test);
	});
	
	//Filter for Linechart
	$('#dp_button_2').click(function() {
		var start = document.getElementById('datepicker_3').value;
		var end = document.getElementById('datepicker_4').value;
		/* rewrite date */
		s = start.split('.');
		start = s[1]+'/'+s[0]+'/'+s[2];
		/* rewrite date */
		e = end.split('.');
		end = e[1]+'/'+e[0]+'/'+e[2];
		start += ' 00:00:00';
		end += ' 23:59:59';
		var tp_start = toTimestamp(start);
		var tp_end = toTimestamp(end);
		if (tp_start <= tp_end){
			var activity_data = [];
			js_activity.forEach(function(item) {
				if (item.timestamp >= tp_start && item.timestamp <= tp_end) {
					activity_data.push({
						date: item.datum,
						accesses: item.zugriffe,
						ownhits: item.ownHits,
						users: item.nutzer
					});
				}		
			});
			var chartData = activity_data.map(function(it){
				var str = it.date;
				r = str.split(', ');
				return [new Date(r[0], r[1], r[2]), it.accesses, it.ownhits, it.users];
			});
			var data = new google.visualization.DataTable();
				data.addColumn('date', 'Datum');
				data.addColumn('number', 'Zugriffe');
				data.addColumn('number', 'eigene Zugriffe');
				data.addColumn('number', 'Nutzer');
				data.addRows(chartData);
			var options = {
				chart: {
					title: 'Zugriffe und Nutzer pro Tag'
				},
				hAxis: {
					title: 'Datum',
					format:'d.M.yy'
				}
			};
			activity_chart.draw(data, options);   
		}else{
			Materialize.toast('Überprüfen Sie ihre Auswahl (Beginn < Ende)', 3000) // 3000 is the duration of the toast
			$('#datepicker_3').val("");
			$('#datepicker_4').val("");
		}	
	});
	
	//Download button for linechart tab.
	$('#html_btn_2').click(function() {
		//Opens dialog box.
		$( "#dialog" ).dialog( "open" );
	});
	
});

// Callback that draws the activity chart.
function drawLineChart() {
  
	var data = new google.visualization.DataTable();
	data.addColumn('date', 'Datum');
	data.addColumn('number', 'Zugriffe');
	data.addColumn('number', 'eigene Zugriffe')
	data.addColumn('number', 'Nutzer');
	data.addRows(linechart_data);

	var options = {
	chart: {
	  title: 'Zugriffe und Nutzer pro Tag'
	},
	hAxis: {
	  title: 'Datum',
	  format:'d.M.yy'
	}


	};

	activity_chart = new google.visualization.LineChart(document.getElementById('linechart'));
	activity_chart.draw(data, options);
  
}