/*JS-file for everything that can be seen on or is related to the heatmap-tab.  Uses language-strings initialised in index.php.*/

$(document).ready(function() {

	//Heatmap - reset button
	$('#rst_btn_3').click(function() {
		drawHeatMap();
		$('#datepicker_5').val("");
		$('#datepicker_6').val("");

	});

	//Filter for Heatmap
	$('#dp_button_3').click(function() {

		var start = document.getElementById('datepicker_5').value;
		var end = document.getElementById('datepicker_6').value;
		/* rewrite date */
		var s = start.split('.');
		start = s[1]+'/'+s[0]+'/'+s[2];
		/* rewrite date */
		var e = end.split('.');
		end = e[1]+'/'+e[0]+'/'+e[2];
		start += ' 00:00:00';
		end += ' 23:59:59';
		var tp_start = toTimestamp(start);
		var tp_end = toTimestamp(end);
		if (tp_start <= tp_end){

				//Create heatmap data
			var timespan;
			var heatmap_data_filtered = [];
			var counterWeekday = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

				//Associative array (object) for total number of weekday actions
			var totalHits = {
				"Monday"  : 0,
				"Tuesday"  : 0,
				"Wednesday"  : 0,
				"Thursday"  : 0,
				"Friday"  : 0,
				"Saturday"  : 0,
				"Sunday"  : 0
			};

				//Associative array (object) for total  number of own weekday actions
			var totalOwnHits = {
				"Monday"  : 0,
				"Tuesday"  : 0,
				"Wednesday"  : 0,
				"Thursday"  : 0,
				"Friday"  : 0,
				"Saturday"  : 0,
				"Sunday"  : 0
			};

			//Associative array (object) to assign the query results
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

				//Iterate through each element of the original query.
			js_heatmap.forEach(function(item) {

					//Check, if the timestamp is included in the filter.
				if (item[1].timecreated >= tp_start && item[1].timecreated <= tp_end) {

						//link timespan to column in heatmap
					if(parseInt(item[1].hour) >= 0  && parseInt(item[1].hour) < 6) {
						timespan = "0to6";
					}
					else if(parseInt(item[1].hour) >= 6  && parseInt(item[1].hour) < 12) {
						timespan = "6to12";
					}
					else if(parseInt(item[1].hour) >= 12  && parseInt(item[1].hour) < 18) {
						timespan = "12to18";
					}
					else if(parseInt(item[1].hour) >= 18  && parseInt(item[1].hour) < 24) {
						timespan = "18to24";
					}

						//Data for specific day
					weekdays[item[1].weekday][timespan]["all"]["value"] += parseInt(item[1].allhits);
					weekdays[item[1].weekday][timespan]["own"]["value"] += parseInt(item[1].ownhits);

						//Data for overall clicks
					totalHits[item[1].weekday] += parseInt(item[1].allhits);
					totalOwnHits[item[1].weekday] += parseInt(item[1].ownhits);


				}
			});

				//Put data of each weekdayfield into suitable format for the chart.
			var counter = 0;
			while (counter <= 6) {

				heatmap_data_filtered.push([weekdays[counterWeekday[counter]]['0to6']['all']['col'],weekdays[counterWeekday[counter]]['row'],weekdays[counterWeekday[counter]]['0to6']['all']['value']]);

				heatmap_data_filtered.push([weekdays[counterWeekday[counter]]['0to6']['own']['col'],weekdays[counterWeekday[counter]]['row'],weekdays[counterWeekday[counter]]['0to6']['own']['value']]);

				heatmap_data_filtered.push([weekdays[counterWeekday[counter]]['6to12']['all']['col'],weekdays[counterWeekday[counter]]['row'],weekdays[counterWeekday[counter]]['6to12']['all']['value']]);

				heatmap_data_filtered.push([weekdays[counterWeekday[counter]]['6to12']['own']['col'],weekdays[counterWeekday[counter]]['row'],weekdays[counterWeekday[counter]]['6to12']['own']['value']]);

				heatmap_data_filtered.push([weekdays[counterWeekday[counter]]['12to18']['all']['col'],weekdays[counterWeekday[counter]]['row'],weekdays[counterWeekday[counter]]['12to18']['all']['value']]);

				heatmap_data_filtered.push([weekdays[counterWeekday[counter]]['12to18']['own']['col'],weekdays[counterWeekday[counter]]['row'],weekdays[counterWeekday[counter]]['12to18']['own']['value']]);

				heatmap_data_filtered.push([weekdays[counterWeekday[counter]]['18to24']['all']['col'],weekdays[counterWeekday[counter]]['row'],weekdays[counterWeekday[counter]]['18to24']['all']['value']]);

				heatmap_data_filtered.push([weekdays[counterWeekday[counter]]['18to24']['own']['col'],weekdays[counterWeekday[counter]]['row'],weekdays[counterWeekday[counter]]['18to24']['own']['value']]);

				counter = counter + 1;
			}

				//Put data of overall clicks into suitable format for the chart.
			var x = 8; //for total and average hits
			while(x <= 11) {
				var y = 0; //for weekdays
				while(y <= 6) {
					if (x == 8) {
						heatmap_data_filtered.push([x, y, totalHits[counterWeekday[y]]]);
					}
					else if (x == 9) {
						heatmap_data_filtered.push([x, y, totalOwnHits[counterWeekday[y]]]);
					}
					else if (x == 10) {
						heatmap_data_filtered.push([x, y, Math.round(totalHits[counterWeekday[y]]/7.0)]);
					}
					else if (x == 11) {
						heatmap_data_filtered.push([x, y, Math.round(totalOwnHits[counterWeekday[y]]/7.0)]);
					}

					y  = y+1;
				}
				x = x+1;
			}


			//initialize heatmap chart
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
					data: heatmap_data_filtered, //convert data string to array
					dataLabels: {
						enabled: true,
						color: '#000000'
					}
				}]

			});
		}else{
			// Materialize.toast(message, displayLength, className, completeCallback);
			Materialize.toast(heatmap_checkSelection, 3000) // 4000 is the duration of the toast
			$('#datepicker_5').val("");
			$('#datepicker_6').val("");
		}

	});

	//Download button for heatmap tab.
	$('#html_btn_3').click(function() {
		//Opens dialog box.
		$( "#dialog" ).dialog( "open" );
	});
});



//Callback that draws the heatmap.
//See highcharts documentation for heatmap.
function drawHeatMap() {
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
			data: heatmap_data,
			dataLabels: {
				enabled: true,
				color: '#000000'
			}
		}]

	});
}
