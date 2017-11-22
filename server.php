<?php

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);

if (!is_null($events)) {
	if (!is_null($events['temperature'])) {
		UpdateTempToDB($events['temperature'], $events['location']);
	}
	foreach ($events['server'] as $event) {
		if (!is_null($event)) {
			UpdateServToDB($event['name'], $event['status'], $events['location']);
		}
	}
}
/**********************************************************************************************************************************/
function UpdateTempToDB($curr_temperature, $location) {
	$db = pg_connect("host=ec2-54-243-187-133.compute-1.amazonaws.com 
					port=5432 
					dbname=dfusod038c3j35 
					user=mmbbbssobrmqjs 
					password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa");

	$result = pg_query($db, "UPDATE tbhlinebottemploc SET temperature = '$curr_temperature' WHERE location = '$location'");	

	// if (!$result) {
	// 	echo "An error occurred.";
	// }			
	// else {
	// 	echo "Updated database successful, please check on your database.";
	// }
}
/**********************************************************************************************************************************/
function UpdateServToDB($name, $status, $location) {
	$db = pg_connect("host=ec2-54-243-187-133.compute-1.amazonaws.com 
					port=5432 
					dbname=dfusod038c3j35 
					user=mmbbbssobrmqjs 
					password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa");

	$loc_id = findLocationID($location);
	$res = ($status) ? 'ON' : 'OFF';
	$result = pg_query($db, "UPDATE tbhlinebotserv SET status = '$res', location_id = '$loc_id' WHERE ip_addr = '$name'");

	// if (!$result) {
	// 	echo "\r\nAn error occurred.";
	// }			
	// else {
	// 	echo "\r\nUpdated database successful, please check on your database.";
	// }
}
/**********************************************************************************************************************************/
function findLocationID($loc_name) {
	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);

	$query = 'SELECT id, location FROM tbhlinebottemploc ORDER BY id ASC';
	$result = $db->query($query);

	$locs = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $locs[$index] = array();
		$locs[$index]['id'] = $row["id"];
		$locs[$index]['loc'] = htmlspecialchars($row["location"]);
		$index = $index + 1;
	}
	$result->closeCursor();

	foreach ($locs as $loc) {
		if ($loc_name == $loc['loc']) {
			return $loc['id'];
		}
	}
	return 0;
}