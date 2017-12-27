<?php
include 'utilities.php';
date_default_timezone_set("Asia/Bangkok");

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
	
	if (date('H') == 7 && date('i') < 5) {
	   	$db = new PDO($GLOBALS['dsn']);
		$query = "SELECT user_id FROM tbhlinebotmem WHERE id_type != 'user'";
		$result = $db->query($query);

		$groups = array();
		$index = 0;
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		    $groups[$index] = htmlspecialchars($row["user_id"]);
			$index += 1;
		}
		$result->closeCursor();
		
		foreach ($groups as $group) {
			BotPush($group, StartJokeQuestion($group));
			//Maybe add random function if have to many groups registered. 
		}
	}
	else if (date('H') == 9 && date('i') < 5) {
		CheckBirthDay($db);
	}

	if ($events['request']) {
		//echo all server list back to client
		GetServerNameList();
	}
	else {
		if (!is_null($events['temperature'])) {
			UpdateTempToDB($events['temperature'], $events['location']);
		}
		if (!is_null($events['humidity'])) {
			UpdateHumidToDB($events['humidity'], $events['location']);
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
			BotPush($adm, $message);
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
function UpdateHumidToDB($curr_humidity, $location) {
	$db = pg_connect($GLOBALS['pgsql_conn']);

	$result = pg_query($db, "UPDATE tbhlinebottemploc SET humidity = '$curr_humidity' WHERE location = '$location'");	

	if ($curr_humidity > 50) {
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
		$message = "ขณะนี้ที่ " . $location . " มีค่าความชื้นมากกว่า " . $curr_humidity . "% แล้ว เพื่อความถูกต้องกรุณาตรวจสอบด้วยตัวของท่านเอง";
		foreach ($admin as $adm) {
			BotPush($adm, $message);
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
				BotPush($adm, $message);
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
		// 		BotPush($adm, $message);
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
function BotPush($admin, $msg) {
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
/**********************************************************************************************************************************/
/**********************************************************************************************************************************/
/**********************************************************************************************************************************/
function CheckBirthDay($db) {
	$query = "SELECT user_id, date_of_birth FROM tbhlinebotmem WHERE id_type = 'user'";
	$result = $db->query($query);

	$bd = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$bd = array();
	    $bd[$index]['uid'] = htmlspecialchars($row["user_id"]);
	    $bd[$index]['bd'] = htmlspecialchars($row["date_of_birth"]);
		$index += 1;
	}
	$result->closeCursor();

	$user = 0;
	$born = array();
	foreach ($bd as $hbd) {
		$d_of_b = substr($hbd['bd'], 5, 5);
		if ($d_of_b == date("m-d")) {
  			$born[$user] = array();
  			$born[$user]['uid'] = $hbd['uid'];
  			$born[$user]['age'] = date("Y") - substr($hbd['bd'], 0, 4);
  			$user += 1;
		}
	}

	if (!empty($born)) {
		foreach ($born as $item) {
			BotPush($item['uid'], CreateMsg($item['age'], $db));
		}
	}
}
/**********************************************************************************************************************************/
function CreateMsg($age, $db) {
	$prefix = array('0' => 'ยินดีด้วยน้าาา อายุ ', '1' => 'อุ๊ย อายุ ', '2' => 'โหวววว อายุ ', '3' => 'สุขสันต์วันเกิดนะครับ อายุ');

	$word = 'text';
	$query = "SELECT $word FROM tbhlinebotans WHERE type = '14'";
	$result = $db->query($query);

	$wishes = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$wishes = array();
	    $wishes[$index] = htmlspecialchars($row["text"]);
		$index += 1;
	}
	$result->closeCursor();

	if ($age <= 25) {
		return $prefix[0] . $age . $wishes[rand(0, 3)];				// rand(0, (count($wishes) / 2) - 1)
	}
	else if ($age > 25 && $age <= 40) {
		return $prefix[rand(1, 2)] . $age . $wishes[rand(0, 3)];	
	}
	else {
		return $prefix[3] . $age . $wishes[rand(4, 7)];				// rand(count($wishes) / 2, count($wishes) - 1)
	}
}
/**********************************************************************************************************************************/
function StartJokeQuestion($userId) {
	$db = new PDO($GLOBALS['dsn']);

	$first_query = "SELECT bot_mode FROM tbhlinebotmodchng WHERE user_id = '$userId'";
	$first_result = $db->query($first_query);
	while ($row = $first_result->fetch(PDO::FETCH_ASSOC)) {
		$bot_mod = htmlspecialchars($row["bot_mode"]);
	}
	$first_result->closeCursor();

	$greeting = array('0' => 'สวัสดีตอนเช้าคับทุกคนนนนนน อากาศตอนเช้าสบายดีมั้ย ขอเสียงโหน่ยยยยยยย',
					  '1' => 'เช้าแล้วน้าาา ทุกคนตอนนี้เป็นไงกันบ้าง ขอเสียงโหน่ยยยยยยย',
					  '2' => 'ทำไมมันเงียบจังน้า ทำไมมันถึงเงียบกว่าชาวบ้านเค้า');

	if (!isset($bot_mod) || $bot_mod != 'allow') {
		return $greeting[rand(0, 2)];
	}

	$query = "SELECT id, question FROM tbhlinebotjokeq ORDER BY id ASC"; 
	$result = $db->query($query);

	$joker = array();
	$seq = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$joker[$seq] = array();
	    $joker[$seq]['id'] = htmlspecialchars($row["id"]);
	    $joker[$seq]['question'] = htmlspecialchars($row["question"]);
	    $seq += 1;
	}
	$result->closeCursor();
	
	$num = rand(0, $seq - 1);
	$joke_id = $joker[$num]['id'];
	$joke_q = $joker[$num]['question'];

	$db2 = pg_connect($GLOBALS['pgsql_conn']);
	$result2 = pg_query($db2, "UPDATE tbhlinebotmodchng SET bot_mode = 'joke', seq = '$joke_id' where user_id = '$userId'");

	return $greeting[rand(0, 2)] . "\nมาทายปัญหากันดีกว่า\n" . $joke_q . '?';
}
/**********************************************************************************************************************************/