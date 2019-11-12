<?php

	# load configuration file 
	require_once 'config.php';

	#$courseID = $_GET['id'];
	#$userID = $_GET['user'];

	# initialisation of the database with individual login details 
	# make a connection to the database
    $dbLink = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); 

    # check if the connection worked out  
    if (!$dbLink){  
    	die("Keine Verbindung zur Datenbank möglich: ".mysql_error()); 
	}
	
	
	# SQL Query -> ActivityChart (date, hits, user counter)	
	$query = "SELECT  FROM_UNIXTIME (timecreated, '%d-%m-%Y') AS 'date', COUNT(action) AS 'allHits', count(DISTINCT userid) AS 'users', COUNT(case when userid = $userID then $userID end) AS 'ownHits'
	FROM `mdl_logstore_standard_log` 
	WHERE (action = 'viewed' AND courseid = '".$courseID."')
	#GROUP BY FROM_UNIXTIME (timecreated, '%m-%d-%y')
	GROUP BY FROM_UNIXTIME (timecreated, '%y-%m-%d')
	ORDER BY 'Datum'"; #Warum 'Datum' statt 'date'?--> aber kein Unterschied zu sehen.
	
	#Alternative: exchange overall actions with only Logins --> WHERE action='loggedin'
	
	
	$result = mysqli_query($dbLink, $query) or die ("Error: ".mysqli_error($dbLink)); 

	# create array to save fetched results | create Object
	$activity = array();
	$counter = 0;
	class Activity {
		public $datum;
		public $zugriffe;
		public $nutzer;
		public $ownHits;
		public $timestamp;
	}

	# fetch results
	while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))  { 
		if (!empty($row["allHits"])) {
			# new Object
			${"activity".$counter} = new Activity;

			#split date
			$teile = explode("-", $row["date"]);
			$tag = $teile[0];
			$monat = $teile[1]-1;
			$jahr = $teile[2];
			$zsmDatum = $jahr.', '.$monat.', '.$tag;

			# $activity.$counter->datum
			${"activity".$counter}->datum = $zsmDatum;

			# $activity.$counter->zugriffe
			${"activity".$counter}->zugriffe = $row["allHits"];

			# $activity.$counter->nutzer
			${"activity".$counter}->nutzer = $row["users"];

			#create timestamp
			$preTimestamp = $row["date"].' 08:00:00';
			$createTimestamp = strtotime($preTimestamp);

			#${"activity".$counter}->timestamp
			${"activity".$counter}->timestamp = $createTimestamp;

			#${"activity".$counter}->ownHits
			${"activity".$counter}->ownHits = $row["ownHits"];

			# write created object to array
			$activity[] = ${"activity".$counter};
		}
	$counter += 1;
	} 

	# loop activity array and create data for non-existing days
	$finalLineChartData = array();
	$finalLineChartObject = array();
	$nextday = $activity[0]->timestamp;
	$counter = 1;

	foreach ($activity as $a){

		if ($nextday != $a->timestamp) {
			# ZERO DATA
			$dateYear = date("Y", $nextday);
			$dateMonth = date("n", $nextday);
			$dateMonth -= 1;
			$dateDay = date("d", $nextday);
			$dateFinal = $dateYear.', '.$dateMonth.', '.$dateDay;
			#$finalLineChartData[] = array("new Date(".$dateFinal.")", 0, 0, $nextday, 0);
			$nextday = strtotime('+1 day', $nextday);
			$boolean = true;

			# using Objects
			${"final".$counter} = new Activity;
			${"final".$counter}->datum = $dateFinal;
			${"final".$counter}->zugriffe = 0;
			${"final".$counter}->nutzer = 0;
			${"final".$counter}->timestamp = $nextday;
			${"final".$counter}->ownHits = 0;
			$finalLineChartObject[] = ${"final".$counter};
			$counter += 1;

			while($boolean == true){
				# DB DATA
				if ($nextday == $a->timestamp){
					#$finalLineChartData[] = array("new Date(".$a->datum.")", $a->zugriffe, $a->ownHits, $a->timestamp ,$a->nutzer);
					$nextday = strtotime('+1 day', $a->timestamp);
					$boolean = false;

					# using object
					${"final".$counter} = new Activity;
					${"final".$counter}->datum = $a->datum;
					${"final".$counter}->zugriffe = $a->zugriffe;
					${"final".$counter}->nutzer = $a->nutzer;
					${"final".$counter}->timestamp = $a->timestamp;
					${"final".$counter}->ownHits = $a->ownHits;
					$finalLineChartObject[] = ${"final".$counter};
					$counter += 1;


				}else{
					# ZERO DATA
					$dateYear = date("Y", $nextday);
					$dateMonth = date("n", $nextday);
					$dateMonth -= 1;
					$dateDay = date("d", $nextday);
					$dateFinal = $dateYear.', '.$dateMonth.', '.$dateDay;
					#$finalLineChartData[] = array("new Date(".$dateFinal.")", 0, 0, $nextday, 0);
					$nextday = strtotime('+1 day', $nextday);
					$boolean = true;

					# using Objects

					${"final".$counter} = new Activity;
					${"final".$counter}->datum = $dateFinal;
					${"final".$counter}->zugriffe = 0;
					${"final".$counter}->nutzer = 0;
					${"final".$counter}->timestamp = $nextday;
					${"final".$counter}->ownHits = 0;
					$finalLineChartObject[] = ${"final".$counter};
					$counter += 1;
				}
			}
			continue;
		}
		# DB DATA

		# using object
		${"final".$counter} = new Activity;
		${"final".$counter}->datum = $a->datum;
		${"final".$counter}->zugriffe = $a->zugriffe;
		${"final".$counter}->nutzer = $a->nutzer;
		${"final".$counter}->timestamp = $a->timestamp;
		${"final".$counter}->ownHits = $a->ownHits;
		$finalLineChartObject[] = ${"final".$counter};

		$nextday = strtotime('+1 day', $a->timestamp);
		$counter += 1;
	}

	# create data string for line chart
	$f = 0;
	$length = count($finalLineChartObject);
	$lineChart ="";
	$lineChartArray = array();

	foreach($finalLineChartObject as $fO){
		if ($f < $length - 1){
			$lineChart .= "[new Date(".$fO->datum."), ".$fO->zugriffe.", ".$fO->ownHits.", ".$fO->nutzer."],";
		}
		if($f == $length -1){
			$lineChart .= "[new Date(".$fO->datum."), ".$fO->zugriffe.", ".$fO->ownHits.", ".$fO->nutzer."]";
		}
		$lineChartArray[] = array("new Date(".$fO->datum.")", $fO->zugriffe, $fO->ownHits, $fO->nutzer, $fO->timestamp);
		$f++;
	}

	# SQL Query for bar chart data
	
	$query_barchart_data = "SELECT RESOURCE.id, name, counter_hits, counter_user 
	FROM (SELECT contextid, courseid, objectid, userid, count(objectid) AS counter_hits, count(DISTINCT userid) AS counter_user FROM mdl_logstore_standard_log WHERE `action`='viewed' AND `target`='course_module' GROUP BY courseid, objectid) AS LOGS 
	JOIN (SELECT id FROM mdl_course) AS COURSE ON LOGS.courseid = COURSE.id
	JOIN (SELECT mdl_resource.id, name, timemodified FROM mdl_resource) AS RESOURCE ON LOGS.objectid = RESOURCE.id WHERE `courseid` = '".$courseID."'";
	
	
	# perform SQL-Query 
	$barchart = $DB->get_records_sql($query_barchart_data);
		
	#create bar chart data
	$j = 1;
	$leng = count($barchart);
	$barchart_data_array = array();
	$bar_chart_data = "[['Dateiname', 'Zugriffe', 'Nutzer'],";

	foreach($barchart as $bar){
		if ($j < $leng ){
			$bar_chart_data .= "['".$bar->name."', ".$bar->counter_hits.", ".$bar->counter_user."],";
		}
		if($j == $leng ){
			$bar_chart_data .= "['".$bar->name."', ".$bar->counter_hits.", ".$bar->counter_user."]]";
	
		}
		$barchart_data_array[] = array($bar->name, $bar->counter_hits, $bar->counter_user);
		$j++;
	}


	#Query for heatmap. Only minor changes to activity chart query.

	$query_heatmap = "SELECT  id, timecreated, FROM_UNIXTIME(timecreated, '%W') AS 'weekday', FROM_UNIXTIME(timecreated, '%k') AS 'hour', COUNT(action) AS 'allHits',  COUNT(case when userid = $userID then $userID end) AS 'ownHits'
	FROM `mdl_logstore_standard_log` 
	WHERE (action = 'viewed' AND courseid = '".$courseID."')
	GROUP BY timecreated"; //group by hour
	
	$heatmap = $DB->get_records_sql($query_heatmap);


		#Create heatmap data
	$timespan;
	$heatmap_data = "[";
	$counterWeekday = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	
		#Array for total number of weekday actions
	$totalHits = array(
		"Monday"  => 0,
		"Tuesday"  => 0,
		"Wednesday"  => 0,
		"Thursday"  => 0,
		"Friday"  => 0,
		"Saturday"  => 0,
		"Sunday"  => 0,
	);
	
		#Array for total  number of own weekday actions
	$totalOwnHits = array(
		"Monday"  => 0,
		"Tuesday"  => 0,
		"Wednesday"  => 0,
		"Thursday"  => 0,
		"Friday"  => 0,
		"Saturday"  => 0,
		"Sunday"  => 0,
	);
	
	{#Array to assign the query results (inside curly braces to hide block)
	$weekdays = array(
		"Monday" => array(
			"0to6" => array(
				"all" => array(
					"col"  => 0,
					"value" => 0,
				),
				"own" => array(
					"col"  => 1,
					"value" => 0,
				),
			),
			"6to12" => array(
				"all" => array(
					"col"  => 2,
					"value" => 0,
				),
				"own" => array(
					"col"  => 3,
					"value" => 0,
				),
			),
				
			"12to18" => array(
				"all" => array(
					"col"  => 4,
					"value" => 0,
				),
				"own" => array(
					"col"  => 5,
					"value" => 0,
				),
			),
				
			"18to24" => array(
				"all" => array(
					"col"  => 6,
					"value" => 0,
				),
				"own" => array(
					"col"  => 7,
					"value" => 0,
				),
			),
			"row" => 0,
		), 
		"Tuesday" => array(
			"0to6" => array(
				"all" => array(
					"col"  => 0,
					"value" => 0,
				),
				"own" => array(
					"col"  => 1,
					"value" => 0,
				),
			),
			"6to12" => array(
				"all" => array(
					"col"  => 2,
					"value" => 0,
				),
				"own" => array(
					"col"  => 3,
					"value" => 0,
				),
			),
				
			"12to18" => array(
				"all" => array(
					"col"  => 4,
					"value" => 0,
				),
				"own" => array(
					"col"  => 5,
					"value" => 0,
				),
			),
				
			"18to24" => array(
				"all" => array(
					"col"  => 6,
					"value" => 0,
				),
				"own" => array(
					"col"  => 7,
					"value" => 0,
				),
			),
			"row" => 1,
		), 
		"Wednesday" => array(
			"0to6" => array(
				"all" => array(
					"col"  => 0,
					"value" => 0,
				),
				"own" => array(
					"col"  => 1,
					"value" => 0,
				),
			),
			"6to12" => array(
				"all" => array(
					"col"  => 2,
					"value" => 0,
				),
				"own" => array(
					"col"  => 3,
					"value" => 0,
				),
			),
				
			"12to18" => array(
				"all" => array(
					"col"  => 4,
					"value" => 0,
				),
				"own" => array(
					"col"  => 5,
					"value" => 0,
				),
			),
				
			"18to24" => array(
				"all" => array(
					"col"  => 6,
					"value" => 0,
				),
				"own" => array(
					"col"  => 7,
					"value" => 0,
				),
			),
			"row" => 2,
		), 
		"Thursday" => array(
			"0to6" => array(
				"all" => array(
					"col"  => 0,
					"value" => 0,
				),
				"own" => array(
					"col"  => 1,
					"value" => 0,
				),
			),
			"6to12" => array(
				"all" => array(
					"col"  => 2,
					"value" => 0,
				),
				"own" => array(
					"col"  => 3,
					"value" => 0,
				),
			),
				
			"12to18" => array(
				"all" => array(
					"col"  => 4,
					"value" => 0,
				),
				"own" => array(
					"col"  => 5,
					"value" => 0,
				),
			),
				
			"18to24" => array(
				"all" => array(
					"col"  => 6,
					"value" => 0,
				),
				"own" => array(
					"col"  => 7,
					"value" => 0,
				),
			),
			"row" => 3,
		), 
		"Friday" => array(
			"0to6" => array(
				"all" => array(
					"col"  => 0,
					"value" => 0,
				),
				"own" => array(
					"col"  => 1,
					"value" => 0,
				),
			),
			"6to12" => array(
				"all" => array(
					"col"  => 2,
					"value" => 0,
				),
				"own" => array(
					"col"  => 3,
					"value" => 0,
				),
			),
				
			"12to18" => array(
				"all" => array(
					"col"  => 4,
					"value" => 0,
				),
				"own" => array(
					"col"  => 5,
					"value" => 0,
				),
			),
				
			"18to24" => array(
				"all" => array(
					"col"  => 6,
					"value" => 0,
				),
				"own" => array(
					"col"  => 7,
					"value" => 0,
				),
			),
			"row" => 4,
		), 
		"Saturday" => array(
			"0to6" => array(
				"all" => array(
					"col"  => 0,
					"value" => 0,
				),
				"own" => array(
					"col"  => 1,
					"value" => 0,
				),
			),
			"6to12" => array(
				"all" => array(
					"col"  => 2,
					"value" => 0,
				),
				"own" => array(
					"col"  => 3,
					"value" => 0,
				),
			),
				
			"12to18" => array(
				"all" => array(
					"col"  => 4,
					"value" => 0,
				),
				"own" => array(
					"col"  => 5,
					"value" => 0,
				),
			),
				
			"18to24" => array(
				"all" => array(
					"col"  => 6,
					"value" => 0,
				),
				"own" => array(
					"col"  => 7,
					"value" => 0,
				),
			),
			"row" => 5,
		), 
		"Sunday" => array(
			"0to6" => array(
				"all" => array(
					"col"  => 0,
					"value" => 0,
				),
				"own" => array(
					"col"  => 1,
					"value" => 0,
				),
			),
			"6to12" => array(
				"all" => array(
					"col"  => 2,
					"value" => 0,
				),
				"own" => array(
					"col"  => 3,
					"value" => 0,
				),
			),
				
			"12to18" => array(
				"all" => array(
					"col"  => 4,
					"value" => 0,
				),
				"own" => array(
					"col"  => 5,
					"value" => 0,
				),
			),
				
			"18to24" => array(
				"all" => array(
					"col"  => 6,
					"value" => 0,
				),
				"own" => array(
					"col"  => 7,
					"value" => 0,
				),
			),
			"row" => 6,
		), 
	);
	}
	
	foreach ($heatmap as $heat) {
		
		#link timespan to column in heatmap
		if((int)$heat->hour >= 0  && (int)$heat->hour < 6) {
			$timespan = "0to6";		
		}
		elseif((int)$heat->hour >= 6  && (int)$heat->hour < 12) {
			$timespan = "6to12";			
		}
		elseif((int)$heat->hour >= 12  && (int)$heat->hour < 18) {
			$timespan = "12to18";				
		}
		elseif((int)$heat->hour >= 18  && (int)$heat->hour < 24) {
			$timespan = "18to24";			
		}
		
			#Data for specific day
		$weekdays[$heat->weekday][$timespan]["all"]["value"] += (int)$heat->allhits;
		$weekdays[$heat->weekday][$timespan]["own"]["value"] += (int)$heat->ownhits;
		
			#Data for overall clicks
		$totalHits[$heat->weekday] += (int)$heat->allhits;
		$totalOwnHits[$heat->weekday] += (int)$heat->ownhits;
		
	}
	
		#Put data of each weekdayfield into suitable format for the chart.
	$counter = 0;
	while($counter <= 6) {
		
		#Data for index.php
		$heatmap_data .= "[".$weekdays[$counterWeekday[$counter]]['0to6']['all']['col'].", ".$weekdays[$counterWeekday[$counter]]['row'].", ".$weekdays[$counterWeekday[$counter]]['0to6']['all']['value']."], ";
		
		$heatmap_data .= "[".$weekdays[$counterWeekday[$counter]]['0to6']['own']['col'].", ".$weekdays[$counterWeekday[$counter]]['row'].", ".$weekdays[$counterWeekday[$counter]]['0to6']['own']['value']."], ";
		
		$heatmap_data .= "[".$weekdays[$counterWeekday[$counter]]['6to12']['all']['col'].", ".$weekdays[$counterWeekday[$counter]]['row'].", ".$weekdays[$counterWeekday[$counter]]['6to12']['all']['value']."], ";
		
		$heatmap_data .= "[".$weekdays[$counterWeekday[$counter]]['6to12']['own']['col'].", ".$weekdays[$counterWeekday[$counter]]['row'].", ".$weekdays[$counterWeekday[$counter]]['6to12']['own']['value']."], ";
		
		$heatmap_data .= "[".$weekdays[$counterWeekday[$counter]]['12to18']['all']['col'].", ".$weekdays[$counterWeekday[$counter]]['row'].", ".$weekdays[$counterWeekday[$counter]]['12to18']['all']['value']."], ";
		
		$heatmap_data .= "[".$weekdays[$counterWeekday[$counter]]['12to18']['own']['col'].", ".$weekdays[$counterWeekday[$counter]]['row'].", ".$weekdays[$counterWeekday[$counter]]['12to18']['own']['value']."], ";
		
		$heatmap_data .= "[".$weekdays[$counterWeekday[$counter]]['18to24']['all']['col'].", ".$weekdays[$counterWeekday[$counter]]['row'].", ".$weekdays[$counterWeekday[$counter]]['18to24']['all']['value']."], ";
		
		$heatmap_data .= "[".$weekdays[$counterWeekday[$counter]]['18to24']['own']['col'].", ".$weekdays[$counterWeekday[$counter]]['row'].", ".$weekdays[$counterWeekday[$counter]]['18to24']['own']['value']."], ";

		$counter++;
	}
	
		#Put data of overall clicks into suitable format for the chart.
	$x = 8; #for total and average hits
	while($x <= 11) {
		$y = 0; #for weekdays
		while($y <= 6) {
			if ($x == 8) {
				$heatmap_data .= "[".$x.", ".$y.", ".$totalHits[$counterWeekday[$y]]."]";
			}
			elseif ($x == 9) {
				$heatmap_data .= "[".$x.", ".$y.", ".$totalOwnHits[$counterWeekday[$y]]."]";
			}
			elseif ($x == 10) {
				$heatmap_data .= "[".$x.", ".$y.", ".round(($totalHits[$counterWeekday[$y]]/4.0), 2)."]";
			}
			elseif ($x == 11) {
				$heatmap_data .= "[".$x.", ".$y.", ".round(($totalOwnHits[$counterWeekday[$y]]/4.0), 2)."]";
			}
			
			if ($x < 11 || $y < 6) {
			$heatmap_data .= ", ";
			}
			
			$y++;
		}
		$x++;
	}
	
	$heatmap_data .= "]";
	

	#Use barchart query for treemap
	$treemap = $barchart;
	
	
	#create treemap data
	$color = -50; #variable for node color
	$i = 1;
	$nodeTitle = 'Global'; #variable for node title
	$lengTree = count($treemap);
	$treemap_data_array = array();
	$treemap_data = 
		"[['Name', 'Parent', 'Size', 'Color'],
			['Global', null, 0, 0],";
				#['Dateien', 'Global', 0, 0],";

	foreach($treemap as $tree){
		#if-clause for node title. (Maybe) To be expanded for forum, chat and assignments.
		/*
		if ($tree->filearea == 'content') {
			$nodeTitle = 'Dateien';
		}
		*/
		#else if ()...
		
		if ($i < $lengTree ){
			$treemap_data .= "['".$tree->name."', '".$nodeTitle."', ".$tree->counter_hits.", ".$color."],";
		}
		if($i == $lengTree ){
			$treemap_data .= "['".$tree->name."', '".$nodeTitle."', ".$tree->counter_hits.", ".$color."]]";
		}
		$treemap_data_array[] = array($tree->name, $nodeTitle, $tree->counter_hits, $color);
		$i++;
		$color = $color+10;
	}
	
	# create data_array 
	# data as JSON [activity_data[date, overallHits, ownHits, users], barchart_data[name, hits, users], treemap_data[name, title, hits, color(as int)]]
	$data_array = array();
	$data_array[] = $lineChartArray;
	$data_array[] = $barchart_data_array;
	$data_array[] = $heatmap_data;
	$data_array[] = $treemap_data_array;
	$data_array[] = $heatmap;

	# encode data_array as JSON !JSON_NUMERIC_CHECK
	# gets encoded only to be decoded in lemo_create_html.php,decode probably not necessary
	$allData = json_encode($data_array, JSON_NUMERIC_CHECK);
	
	
	
	#disconnect from the database
	mysqli_close($dbLink); 
	