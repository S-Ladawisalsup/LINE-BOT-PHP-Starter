<?php
include 'utilities.php';

$dsn = 'pgsql:'
	. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
	. 'dbname=dfusod038c3j35;'
	. 'user=mmbbbssobrmqjs;'
	. 'port=5432;'
	. 'sslmode=require;'
	. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

$pgsql_conn = "host=ec2-54-243-187-133.compute-1.amazonaws.com 
					port=5432 
					dbname=dfusod038c3j35 
					user=mmbbbssobrmqjs 
					password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa";

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);

if (!is_null($events)) {
	if ($events['request']) {
		//echo all server list back to client
		GetServerNameList();
	}
	else {
		if (!is_null($events['temperature'])) {
			UpdateTempToDB($events['temperature'], $events['location']);
		}
		foreach ($events['server'] as $event) {
			if (!is_null($event)) {
				UpdateServToDB($event['name'], $event['status'], $events['location']);
			}
		}
	}
}
/**********************************************************************************************************************************/
function UpdateTempToDB($curr_temperature, $location) {
	$db = pg_connect($GLOBALS['pgsql_conn']);

	$result = pg_query($db, "UPDATE tbhlinebottemploc SET temperature = '$curr_temperature' WHERE location = '$location'");	

	if ($curr_temperature > 37) {
		$db_query = new PDO($GLOBALS['dsn']);
		$query2 = "SELECT user_id FROM tbhlinebotmem WHERE position = 'admin'"; 
		$result2 = $db_query->query($query2);

		$admin = array();
		$index = 0;
		while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
		    $admin[$index] = htmlspecialchars($row["user_id"]);
		    $index += 1;
		}
		$result2->closeCursor();
		$message = "ขณะนี้ที่ " . $location . " อุณหภูมิเท่กับ " . $curr_temperature . " องศาเซลเซียส เพื่อความถูกต้องกรุณาตรวจสอบด้วยตัวของท่านเอง";
		foreach ($admin as $adm) {
			BotPush($message, $adm);
		}
	}

	// if (!$result) {
	// 	echo "An error occurred.";
	// }			
	// else {
	// 	echo "Updated database successful, please check on your database.";
	// }
}
/**********************************************************************************************************************************/
function UpdateServToDB($name, $status, $location) {
	$db = pg_connect($GLOBALS['pgsql_conn']);

	$loc_id = findLocationID($location);

	$db_query = new PDO($GLOBALS['dsn']);
	$query = 'SELECT ip_addr, status, lastchangedatetime FROM tbhlinebotserv ORDER BY id ASC';
	$res = $db_query->query($query);
	$previos_status = array();
	$count = 0;
	while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
		$previos_status[$count] = array();
		$previos_status[$count]['ip'] = htmlspecialchars($row["ip_addr"]);
		$previos_status[$count]['stats'] = htmlspecialchars($row["status"]);
		$previos_status[$count]['timer'] = htmlspecialchars($row["lastchangedatetime"]);
		$count += 1;
	}
	$res->closeCursor();
	$backup = false;
	$laststate = 'error';
	$lasttime = date("Y:m:d H:i:s");
	foreach ($previos_status as $pre_state) {
		if ($name == $pre_state['ip']) {
			if ($status != $pre_state['stats']) {
				$laststate = $pre_state['stats'];
				$lasttime = $pre_state['timer'];
				$backup = true;
			}
			break;
		}
	}

	if ($backup) {
		$result = pg_query($db, "UPDATE tbhlinebotserv SET status = '$status', location_id = '$loc_id', 
								lastchangestatus = '$laststate', datetimestatuschanged = '$lasttime' WHERE ip_addr = '$name'");

		if ($status == 'danger') { // or warning for demo
			$query2 = "SELECT user_id FROM tbhlinebotmem WHERE position = 'admin'"; 
			$result2 = $db_query->query($query2);

			$admin = array();
			$index = 0;
			while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
			    $admin[$index] = htmlspecialchars($row["user_id"]);
			    $index += 1;
			}
			$result2->closeCursor();
			$message = "ขณะนี้ server " . $name . " อาจจะไม่สามารถใช้งานได้ เพื่อความถูกต้องกรุณาตรวจสอบด้วยตัวของท่านเอง";
			foreach ($admin as $adm) {
				BotPush($message, $adm);
			}
		}
	}
	else {
		$result = pg_query($db, "UPDATE tbhlinebotserv SET status = '$status', location_id = '$loc_id' WHERE ip_addr = '$name'");

		// if ($status == 'danger') {
		// 	$query2 = "SELECT user_id FROM tbhlinebotmem WHERE position = 'admin'"; 
		// 	$result2 = $db_query->query($query2);

		// 	$admin = array();
		// 	$index = 0;
		// 	while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
		// 	    $admin[$index] = htmlspecialchars($row["user_id"]);
		// 	    $index += 1;
		// 	}
		// 	$result2->closeCursor();
		// 	$message = "ขณะนี้ server " . $name . " อาจจะไม่สามารถใช้งานได้ เพื่อความถูกต้องกรุณาตรวจสอบด้วยตัวของท่านเอง";
		// 	foreach ($admin as $adm) {
		// 		BotPush($message, $adm);
		// 	}
		// }
	}

	// if (!$result) {
	// 	echo "\r\nAn error occurred.";
	// }			
	// else {
	// 	echo "\r\nUpdated database successful, please check on your database.";
	// }
}
/**********************************************************************************************************************************/
function findLocationID($loc_name) {
	$db = new PDO($GLOBALS['dsn']);

	$query = 'SELECT id, location FROM tbhlinebottemploc ORDER BY id ASC';
	$result = $db->query($query);

	$locs = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $locs[$index] = array();
		$locs[$index]['id'] = $row["id"];
		$locs[$index]['loc'] = htmlspecialchars($row["location"]);
		$index += 1;
	}
	$result->closeCursor();

	foreach ($locs as $loc) {
		if ($loc_name == $loc['loc']) {
			return $loc['id'];
		}
	}
	return 0;
}
/**********************************************************************************************************************************/
function GetServerNameList() {
	$db = new PDO($GLOBALS['dsn']);

	$query = 'SELECT ip_addr FROM tbhlinebotserv ORDER BY id ASC';
	$result = $db->query($query);

	$servers = array();
	$list = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $servers[$list] = htmlspecialchars($row["ip_addr"]);
		$list += 1;
	}
	$result->closeCursor();

	$responseJSON = json_encode($servers);
	echo $responseJSON;
}
/**********************************************************************************************************************************/
function BotPush($msg, $admin) {
	$access_token = 'CFecc4UnPdpCUxVk2VuTlf7ANCYHbCpaxYltjR/z15zMJ/KzsPIVrp4tCql4xmQYr8qgJSZ6oitEZ0/PKH+FpdneucSfPgjTP03mQ5KRSKqYT93fEEvGDqOUxJ/SBoS3oTXcJaRSxlPVBWxH+8PWxAdB04t89/1O/w1cDnyilFU=';

	$messages = BotReplyText($msg);

	// Make a POST Request to Messaging API to push to sender
	$url = 'https://api.line.me/v2/bot/message/push';
	$data = [
		'to' => $admin,
		'messages' => [$messages],
	];
	$post = json_encode($data);
	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);
	curl_close($ch);

	echo $result . "\r\n";
}