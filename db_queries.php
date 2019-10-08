<?php
	# database file
	# database queries, chart data, json files

	/*  SQL QUERY - docrank inspired */
	#
	# get number of files for course with $courseID
	#
	/* 
	SELECT count(*) AS count FROM (SELECT contextid, courseid, objectid, count(*) AS counter FROM mdl_logstore_standard_log WHERE `action`='viewed' AND `target`='course_module' GROUP BY courseid, objectid) logs 
	JOIN (SELECT contextid, component, filearea, itemid, filename FROM mdl_files WHERE `filename` != '.') files ON logs.contextid = files.contextid 
	JOIN (SELECT id FROM mdl_course) course ON logs.courseid = course.id 
	JOIN (SELECT mdl_resource.id, name, timemodified FROM mdl_resource) docs ON logs.objectid = docs.id WHERE courseid = '4';
	*/
	
	#
	# get data for bar chart
	#
	/* #1 Query for getting just the file information and the total hits per file
	SELECT docs.id, files.contextid, files.component, files.filearea, files.itemid, files.filename, name, counter, timemodified FROM 
	(SELECT contextid, courseid, objectid, count(*) AS counter FROM mdl_logstore_standard_log WHERE `action`='viewed' AND `target`='course_module' GROUP BY courseid, objectid) logs 
	JOIN (SELECT contextid, component, filearea, itemid, filename FROM mdl_files WHERE `filename` != '.') files ON logs.contextid = files.contextid
	JOIN (SELECT id FROM mdl_course) course ON logs.courseid = course.id
	JOIN (SELECT mdl_resource.id, name, timemodified FROM mdl_resource) docs ON logs.objectid = docs.id WHERE `courseid` = '4'
	*/
	/* #2 Query for getting file information, total hits per file and unique users per file 
	SELECT RESOURCE.id, FILES.contextid, FILES.component, FILES.filearea, FILES.filename, name, counter_hits, counter_user FROM 
	(SELECT contextid, courseid, objectid, userid, count(objectid) AS counter_hits, count(DISTINCT userid) AS counter_user FROM mdl_logstore_standard_log WHERE `action`='viewed' AND `target`='course_module' GROUP BY courseid, objectid) AS LOGS 
	JOIN (SELECT contextid, component, filearea, itemid, filename FROM mdl_files WHERE `filename` != '.') AS FILES ON LOGS.contextid = FILES.contextid
	JOIN (SELECT id FROM mdl_course) AS COURSE ON LOGS.courseid = COURSE.id
	JOIN (SELECT mdl_resource.id, name, timemodified FROM mdl_resource) AS RESOURCE ON LOGS.objectid = RESOURCE.id WHERE `courseid` = '4'
	*/

	#print an array
	#echo '<pre>'; print_r($data_array); echo '</pre>';
	#var_dump($lineChart);

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
	
	/* test for direct query in phpmyadmin
	SELECT FROM_UNIXTIME (timecreated, '%d-%m-%Y') AS 'date', COUNT(action) AS 'allHits', count(DISTINCT userid) AS 'users', COUNT(userid= '4') AS 'ownHits' FROM `mdl_logstore_standard_log` WHERE (action = 'viewed' AND courseid = '2') GROUP BY FROM_UNIXTIME (timecreated, '%y-%m-%d') ORDER BY 'date' 
	*/
	
	
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
#mel 			echo $createTimestamp;
#mel 			echo '<br>';

			#${"activity".$counter}->ownHits
			${"activity".$counter}->ownHits = $row["ownHits"];

			# write created object to array
			$activity[] = ${"activity".$counter};
		}
	$counter += 1;
	} 
#mel echo $counter;
#mel echo '<br><br>';
	# loop activity array and create data for non-existing days
	$finalLineChartData = array();
	$finalLineChartObject = array();
	$nextday = $activity[0]->timestamp;
	$counter = 1;

	foreach ($activity as $a){
#mel 	echo $a->timestamp;
#mel 	echo '<br>';
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
#mel 					echo $counter;
#mel 					echo '\n';
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
		#$finalLineChartData[] = array($a->datum, $a->zugriffe, $a->ownHits, $a->timestamp ,$a->nutzer);

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
	
	/* This Query is not working as expected, a lot  of  unneccessary informations are included
	$query_barchart_data = 
	"SELECT RESOURCE.id, FILES.contextid, FILES.component, FILES.filearea, FILES.filename, name, counter_hits, counter_user 
	FROM (SELECT contextid, courseid, objectid, userid, count(objectid) AS counter_hits, count(DISTINCT userid) AS counter_user FROM mdl_logstore_standard_log WHERE `action`='viewed' AND `target`='course_module' GROUP BY courseid, objectid) AS LOGS 
	JOIN (SELECT contextid, component, filearea, itemid, filename FROM mdl_files WHERE `filename` != '.') AS FILES ON LOGS.contextid = FILES.contextid
	JOIN (SELECT id FROM mdl_course) AS COURSE ON LOGS.courseid = COURSE.id
	JOIN (SELECT mdl_resource.id, name, timemodified FROM mdl_resource) AS RESOURCE ON LOGS.objectid = RESOURCE.id WHERE `courseid` = '".$courseID."'";
	*/
	
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
	#$heatmap_data_array = array();
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
				$heatmap_data .= "[".$x.", ".$y.", ".round(($totalHits[$counterWeekday[$y]]/7.0), 2)."]";
			}
			elseif ($x == 11) {
				$heatmap_data .= "[".$x.", ".$y.", ".round(($totalOwnHits[$counterWeekday[$y]]/7.0), 2)."]";
			}
			
			if ($x < 11 || $y < 6) {
			$heatmap_data .= ", ";
			}
			
			$y++;
		}
		$x++;
	}
	
	$heatmap_data .= "]";

/*

	#Create heatmap data
	
	$rowHeatmap = 0;
	$colAllHeatmap = 0;
	$colOwnHeatmap = 1;
	$lengHeat  = count($heatmap);
	$k = 1;
	$heatmap_data = "[";
	$heatmap_data_array = array();
	
		#Array for total number of weekday actions
	$totalHits = array(
		0,
		0,
		0,
		0,
		0,
		0,
		0,
	);
	
		#Array for total  number of own weekday actions
	$totalOwnHits = array(
		0,
		0,
		0,
		0,
		0,
		0,
		0,
	);
	*/
	
	/*  Noch unsicher, was mit 'average' gemeint ist.
	
		#Array for average number of weekday actions
	$avgHits = array(
		0 = 0,
		1 = 0,
		2 = 0,
		3 = 0,
		4 = 0,
		5 = 0,
		6 = 0,
	);
	
		#Array for average number of own weekday actions
	$totalOwnHits = array(
		0 = 0,
		1 = 0,
		2 = 0,
		3 = 0,
		4 = 0,
		5 = 0,
		6 = 0,
	);
	
	*/
	

	/*
	foreach($heatmap as $heat) {
		
		#link timespan to column in heatmap
		if((int)$heat->hour >= 0  && (int)$heat->hour < 6) {
			$colAllHeatmap = 0;
			$colOwnHeatmap = 1;			
		}
		elseif((int)$heat->hour >= 6  && (int)$heat->hour < 12) {
			$colAllHeatmap = 2;
			$colOwnHeatmap = 3;			
		}
		elseif((int)$heat->hour >= 12  && (int)$heat->hour < 18) {
			$colAllHeatmap = 4;
			$colOwnHeatmap = 5;			
		}
		elseif((int)$heat->hour >= 18  && (int)$heat->hour < 24) {
			$colAllHeatmap = 6;
			$colOwnHeatmap = 7;			
		}
		
		
		#link weekday to row in heatmap
		if($heat->weekday == 'Monday') {
			$rowHeatmap = 0;
			$totalHits[0]  += (int)$heat->allhits;
			$totalOwnHits[0]  += (int)$heat->ownhits;
		}
		elseif($heat->weekday == 'Tuesday') {
			$rowHeatmap = 1;
			$totalHits[1]  += (int)$heat->allhits;
			$totalOwnHits[1]  += (int)$heat->ownhits;
		}
		elseif($heat->weekday == 'Wednesday') {
			$rowHeatmap = 2;
			$totalHits[2]  += (int)$heat->allhits;
			$totalOwnHits[2]  += (int)$heat->ownhits;
		}
		elseif($heat->weekday == 'Thursday') {
			$rowHeatmap = 3;
			$totalHits[3]  += (int)$heat->allhits;
			$totalOwnHits[3]  += (int)$heat->ownhits;
		}
		elseif($heat->weekday == 'Friday') {
			$rowHeatmap = 4;
			$totalHits[4]  += (int)$heat->allhits;
			$totalOwnHits[4]  += (int)$heat->ownhits;
		}
		elseif($heat->weekday == 'Saturday') {
			$rowHeatmap = 5;
			$totalHits[5]  += (int)$heat->allhits;
			$totalOwnHits[5]  += (int)$heat->ownhits;
		}
		elseif($heat->weekday == 'Sunday') {
			$rowHeatmap = 6;
			$totalHits[6]  += (int)$heat->allhits;
			$totalOwnHits[6]  += (int)$heat->ownhits;
		}
		
		$heatmap_data .= "[".$colAllHeatmap.", ".$rowHeatmap.", ".(int)$heat->allhits."], [".$colOwnHeatmap.", ".$rowHeatmap.", ".(int)$heat->ownhits."]";
		$heatmap_data_array[] = array($colAllHeatmap, $rowHeatmap, (int)$heat->allhits);
		$heatmap_data_array[] = array($colOwnHeatmap, $rowHeatmap, (int)$heat->ownhits);
		
		
		#find last element of $heatmap
		if($k < $lengHeat) {
			$heatmap_data .= ", ";
		}
		else {
			$x = 8; #for total and average hits
			while($x <= 11) {
				$y = 0; #for weekdays
				while($y <= 6) {
					if ($x == 8) {
						$heatmap_data .= ", [".$x.", ".$y.", ".$totalHits[$y]."]";
						$heatmap_data_array[] = array($x, $y, $totalHits[$y]);
					}
					elseif ($x == 9) {
						$heatmap_data .= ", [".$x.", ".$y.", ".$totalOwnHits[$y]."]";
						$heatmap_data_array[] = array($x, $y, $totalOwnHits[$y]);
					}
					elseif ($x == 10) {
						$heatmap_data .= ", [".$x.", ".$y.", ".round(($totalHits[$y]/7.0), 2)."]";
						$heatmap_data_array[] = array($x, $y, round(($totalHits[$y]/7.0), 2));
					}
					elseif ($x == 11) {
						$heatmap_data .= ", [".$x.", ".$y.", ".round(($totalOwnHits[$y]/7.0), 2)."]";
						$heatmap_data_array[] = array($x, $y, round(($totalOwnHits[$y]/7.0), 2));
					}
					$y++;
				}
				$x++;
			}
			*/
			/* Not working as planned, colours of cells not added until mouseover.
			#filler for missing days
			$counterRow = 0;
			while($counterRow <= 6) {
				$counterCol = 0;
				while($counterCol <= 11) {
					$heatmap_data .= ", [".$counterCol.",".$counterRow.",0]";
					$heatmap_data_array[] = array($counterCol, $counterRow, 0);
					$counterCol++;
				}
				$counterRow++;
			}*/
			/*
			$heatmap_data.= "]";
		}
		$k++;
	}

*/
	

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
	
	# save data as JSON
	# create data_array (lineChartArray, barchart_data)
	# data as JSON [activity_data[date, overallHits, ownHits, users], barchart_data[name, hits, users], treemap_data[name, title, hits, color(as int)]]
	$data_array = array();
	$data_array[] = $lineChartArray;
	$data_array[] = $barchart_data_array;
	$data_array[] = $heatmap_data;
	$data_array[] = $treemap_data_array;
	$data_array[] = $heatmap;
	
	/*
	# path to json file | filename
	$file = 'saved_datasets/'.$courseID.'_'.$userID.'/data_'.$courseID.'_'.$userID.'.json';
    */

	# encode data_array as JSON !JSON_NUMERIC_CHECK
	# gets encoded only to be decoded in create_html.php,decode probably not necessary
	$allData = json_encode($data_array, JSON_NUMERIC_CHECK);

	/*
	# check if $file exists
	if (file_exists($file)){
		# APPEND (ActivityData)

		# load existing file
		$existingData = json_decode(file_get_contents($file), true);

		# count array length
		$count_exD = count($existingData[0]);		
		$count_newD = count($data_array[0]);

		# get first and last item of existingData
		$first_exD = $existingData[0][0][0];
		$last_exD = $existingData[0][$count_exD-1][0];

		#get first and last item of data_array
		$first_newD = $data_array[0][0][0];
		$last_newD = $data_array[0][$count_newD-1][0];

		# check if dates of the array are equal (length, first / last item)
		if ($first_exD == $first_newD){
			# overwrite whole activity data (dates are equal, hits probably changed)
			file_put_contents($file, $createJSON);
			echo '<center>file existed - file was modified</center>';
		}else{
			# check if last date of existing data exists in new data | get key
					$test = false; 
					foreach($data_array[0] as $key=>$d){
						if($d[0] == $last_exD){
							$test = true;
							$key_pointer = $key;
						}
					}
					if ($test){
						$new_data_array = array();
						$new_activity = array();

						#write existing data to new array
						foreach($existingData[0] as $key=>$eD){
							$new_activity[] = array($eD[0], $eD[1], $eD[2], $eD[3]);
						}
						
						# append new data to array (use $key_pointer)
						for($i = $key_pointer+1; $i <= $count_newD-1; $i++){
							$new_activity[] = array($data_array[0][$i][0], $data_array[0][$i][1], $data_array[0][$i][2], $data_array[0][$i][3]);	
						}
						#add activity data to data array
						$new_data_array[] = $new_activity;

						# check for new bar chart data
						$count_newBar = count($data_array[1]);
						$new_bar_data = array();
						// foreach ($data_array[1] as $key =>$b){
						// 	$new_bar_data[] = array($b[0], $b[1], $b[2]);
						// 	#echo $b[0].$b[1].$b[2].'</br>';
						// }
						$new_data_array[] = $data_array[1];
						$createJSON2 = json_encode($new_data_array, JSON_NUMERIC_CHECK);
						file_put_contents($file, $createJSON2);
						echo '<center>file existed. file was appended</center>';
					}
		}
	}else{
		# file doesn't exist | check if dir exists
		if (!is_dir('saved_datasets/'.$courseID.'_'.$userID)) {
			// dir doesn't exist, make it
			mkdir('saved_datasets/'.$courseID.'_'.$userID);
		}

		# create file
		file_put_contents($file, $createJSON);
		echo '<center>File did not exist. (deleted or never existed before) so it was freshly created</center>';
	}
	*/
	
	
	
	#disconnect from the database
	mysqli_close($dbLink); 
	