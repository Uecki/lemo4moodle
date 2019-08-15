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
	$query_barchart_data = "SELECT RESOURCE.id, FILES.contextid, FILES.component, FILES.filearea, FILES.filename, name, counter_hits, counter_user FROM 
	(SELECT contextid, courseid, objectid, userid, count(objectid) AS counter_hits, count(DISTINCT userid) AS counter_user FROM mdl_logstore_standard_log WHERE `action`='viewed' AND `target`='course_module' GROUP BY courseid, objectid) AS LOGS 
	JOIN (SELECT contextid, component, filearea, itemid, filename FROM mdl_files WHERE `filename` != '.') AS FILES ON LOGS.contextid = FILES.contextid
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

	
	# save data as JSON
	# create data_array (lineChartArray, barchart_data)
	# data as JSON [activity_data[date, overallHits, ownHits, users], barchart_data[name, hits, users]]
	$data_array = array();
	$data_array[] = $lineChartArray;
	$data_array[] = $barchart_data_array;
	
	
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
	