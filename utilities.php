<?php

date_default_timezone_set("Asia/Bangkok");
/**********************************************************************************************************************************/
/*** Function for check word(s) contain(s) start or end at string. ***/
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}
/**********************************************************************************************************************************/
/*** Function generates answer as text type, now get answer from array text file by random (cannot connect datatabase now) ***/
function AnswerBuilder($mood) {
	//----------------------------------------------------------------
	// Old Version
	// switch ($mood) {
	// 	case 'ans':
	// 		$answer = file('text/answer.txt');
	// 		break;		
	// 	default:
	// 		$answer = file('text/reply.txt');
	// 		break;
	// }

	// $building = 'error';
	// if (count($answer) > 0) {
	// 	$numindex = rand(1, count($answer));
	// 	$building = $answer[$numindex];
	// 	$building = substr($building, 0, strlen($building)-1);
	// }
	// return $building;
	//----------------------------------------------------------------
	// New Version
	if ($mood > 11) {
		$notepad = file('text/reply.txt');
		$resultreply = 'ถ้าคุณขับรถบรรทุกคนไป 43 คน เพื่อไปเชียงใหม่ แต่ระหว่างทางคุณรับคนอีก 7 คน เพื่อไปส่งที่ภูเก็ต ถามว่าคนขับชื่ออะไรระหว่าง ควาย กับ หมา?';
		if (count($notepad) > 0) {
			$resultreply = $notepad[rand(1, count($notepad))];
			$resultreply = substr($resultreply, 0, strlen($resultreply)-1);
		}
		return $resultreply;
	}

	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);
	$word = 'text';
	$query = "SELECT $word FROM tbhlinebotans WHERE type = '$mood'";
	$result = $db->query($query);

	$reply = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $reply[$index] = htmlspecialchars($row["text"]);
	    $index = $index + 1;
	}
	$result->closeCursor();

	return $reply[rand(0, count($reply))];
	//----------------------------------------------------------------
}
/**********************************************************************************************************************************/
/*** Function generates answer as sticker type by random from default sticker(s) by LINE Corp. ***/
function GetSticker() {
	$packageId = rand(1, 2);
	$randType = rand(0, 2);

	if ($packageId == 1) {		
		if ($randType == 0) {
			$stickerId = rand(0, 18);
			if ($stickerId == 18) {
				$stickerId = 21;
			}
		}
		else if ($randType == 1) {
			$stickerId = rand(100, 139);
		}
		else {
			$stickerId = rand(401, 430);
		}
	}
	else {
		if ($randType == 0) {
			$stickerId = rand(19, 47);
			if ($stickerId <= 21) {
				$stickerId = $stickerId - 1;
			}
		}
		else if ($randType == 1) {
			$stickerId = rand(140, 179);
		}
		else {
			$stickerId = rand(501, 527);
		}
	}
	return array('packageId' => $packageId, 'stickerId' => $stickerId);
}
/**********************************************************************************************************************************/
function findQuestionType ($text) {
/*******************************************************************
NOTE!
Question has 7 formats!
1. "yes/no" question 
2. "when" question (will answer as timing) 
3. "where" qusetion (will answer as location)
4. "who" question (will answer as person)
5. "what/how" question (will answer as reason)
6. "which" question (will answer as object) 
7. "how+.." question (will answer as number)
Ohter(s) Mode!
8. It's ping to anther devices or server mode
9. Greeting word(s) type mode.
10. Refuse as answer in all response mode.
11. Person mode for answer who's question.
*******************************************************************/
	//Trim start
	$text = str_replace(' ', '', $text);

	$QAArray = QuestionWordFromDB();
	foreach ($QAArray as $keyitems) {
		if ($keyitems['type'] == 4) {
			if (startsWith($text, $keyitems['text']) || endsWith($text, $keyitems['text'])) {
				return $keyitems['type'];
			}
		}
		else if ($keyitems['type'] == 8 || $keyitems['type'] == 9) {
			if (strpos($text, $keyitems['text']) !== false) {
				return $keyitems['type'];
			}
		}
		else if (endsWith($text, $keyitems['text'])) {
			if (($keyitems['type'] == 1 && (strpos($text, 'ล่ม') !== false || strpos($text, 'เจ๊ง') !== false || 
										    strpos($text, 'พัง') !== false || strpos($text, 'ดับ') !== false)) || 
				($keyitems['type'] == 5 && (strpos($text, 'สถานะ') !== false || strpos($text, 'สเตตัส') !== false || 
										   	strpos($text, 'status') !== false))) {
				return 8;
			}
			else {
				return $keyitems['type'];
			}
		}
	}
	return 0;
}
/**********************************************************************************************************************************/
function QuestionWordFromDB() {
	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);

	$query = 'SELECT id, questiontext, questiontype FROM tbhlinebotwmode ORDER BY id ASC';
	$result = $db->query($query);

	$qwords = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $qwords[$index] = array();
		$qwords[$index]['text'] = htmlspecialchars($row["questiontext"]);
		$qwords[$index]['type'] = htmlspecialchars($row["questiontype"]);
		$index = $index + 1;
	}
	$result->closeCursor();

	return $qwords;
}
/**********************************************************************************************************************************/
function GetTemperature($text) {
	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);

	$query_loccall = 'SELECT id, loc_callname, loc_id FROM tbhlinebotlocname ORDER BY id ASC';
	$result = $db->query($query_loccall);

	$locations = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $locations[$index] = array();
		$locations[$index]['name'] = htmlspecialchars($row["loc_callname"]);
		$locations[$index]['id'] = htmlspecialchars($row["loc_id"]);
		$index = $index + 1;
	}
	$result->closeCursor();

	$curr_place = 0;
	foreach ($locations as $locate) {
		if (strpos($text, $locate['name']) !== false) {
			$curr_place = $locate['id'];
			$curr_locname = $locate['name'];
			break;
		}
	}

	$tempresult = 'ไม่มีที่ ' . $curr_locname . ' น๊ะจ๊ะ อยากรู้เดินไปดูเองเลยจ้า';
	if ($curr_place != 0) {
		$query_locnametemp = "SELECT temperature, lastchangedatetime AT TI"
							."ME ZONE 'UTC+7' as lastchangedatetime FROM tbhlinebottemploc WHERE id = $curr_place";
		$results = $db->query($query_locnametemp);
		$last_temp = array();
		while ($row = $results->fetch(PDO::FETCH_ASSOC)) {  
			$last_temp['temp'] = htmlspecialchars($row["temperature"]);
			$last_temp['datetime'] = substr(htmlspecialchars($row["lastchangedatetime"]), 0, 16);
		}
		$results->closeCursor();

		if (substr($last_temp['datetime'], 0, 10) == date("Y-m-d")) {
			//lastchangedatetime == datenow, tell only time
			$previous_time = substr($last_temp['datetime'], 11);
			$previous_time = str_replace(':', '.', $previous_time);
			$tempresult = 'ล่าสุดเมื่อเวลา ' . $previous_time . 'น. อุณหภูมิที่' . $curr_locname . 'เท่ากับ ' . $last_temp['temp'] . ' องศาเซลเซียส จ้า';
		}
		else {
			//lastchangedatetime != datenow, tell date and time
			$previous_date = date("d/m/Y", strtotime(substr($last_temp['datetime'], 0, 10)));
			$previous_time = substr($last_temp['datetime'], 11);
			$previous_time = str_replace(':', '.', $previous_time);
			$tempresult = 'ล่าสุดเมื่อวันที่ ' . $previous_date . ' เวลา ' . $previous_time . 'น. อุณหภูมิที่' . $curr_locname . 'เท่ากับ ' . $last_temp['temp'] . ' องศาเซลเซียส จ้า';
		}
	}
	return $tempresult;
}
/**********************************************************************************************************************************/
function IsAskedServer($text) {
	
	$ip_addr = array('IsChecked' => false, 'ip_addr' => '127.0.0.1');

	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);

	$query = 'SELECT id, ip_addr, serv_name, last_ip FROM tbhlinebotserv ORDER BY id ASC';
	$result = $db->query($query);

	$servers = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $servers[$index] = array();
		$servers[$index]['ip'] = htmlspecialchars($row["ip_addr"]);
		$servers[$index]['name'] = htmlspecialchars($row["serv_name"]);
		$servers[$index]['lip'] = htmlspecialchars($row["last_ip"]);
		$index = $index + 1;
	}
	$result->closeCursor();

	$serv_iden = array('0' => '20.',  //<-- these case and other check string after str_replace 192.1. and . until result 5-6 digit num
					   '1' => '100.', //
					   '2' => '101.', // 
					   '3' => '102.', //
					   '4' => 'เบอร์'); //<-- only this case check server['lip']

	$ip_req = 1000000;
	for ($iden = 0; $iden <= 4; $iden++) {
		if (strpos($text, $serv_iden[$iden]) !== false) {
			$tmptext = str_replace('192.1.', '', $text);
			$tmptext = str_replace('.', '', $tmptext);
			preg_match_all('!\d+\.*\d*!', $tmptext, $matches);
			$val = $matches[0];
			$ip_req = $val[0];
		}
	}

	foreach ($servers as $server) {
		//must be careful with duplicated server name, now set false to all duplicated server name.
		if (strpos(strtolower($text), strtolower($server['name'])) !== false && $server['name'] != 'PrinterServerS') {		
			$ip_addr['IsChecked'] = true;
			$ip_addr['ip_addr'] = $server['ip'];
			break;
		}
		else if ($ip_req < 1000000) {
			if ($ip_req < 1000) {
				if ($ip_req == $server['lip']) {
					$ip_addr['IsChecked'] = true;
					$ip_addr['ip_addr'] = $server['ip'];
					break;
				}
			}
			else {
				$tempip = str_replace('192.1.', '', $server['ip']);
				$tempip = str_replace('.', '', $tempip);
				if ($ip_req == $tempip) {
					$ip_addr['IsChecked'] = true;
					$ip_addr['ip_addr'] = $server['ip'];
					break;
				}
			}
		}
	}

	return $ip_addr;
}
/**********************************************************************************************************************************/
function GetPingAnswer($ip_address) {
	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);

	$query = "SELECT status, lastchangedatetime AT TI"
							."ME ZONE 'UTC+7' as lastchangedatetime FROM tbhlinebotserv WHERE ip_addr = '$ip_address'";
	$result = $db->query($query);

	$server = array();
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$server['status'] = htmlspecialchars($row["status"]);
		$server['timer'] = substr(htmlspecialchars($row["lastchangedatetime"]), 0, 16);
	}
	$result->closeCursor();

	$normally = ($server['status'] == 'ON') ? ' ปกติดีจ้า' : ' น่าจะมีปัญหาแล้วหล่ะ กำลังติดต่อผู้เกี่ยวข้องให้แก้ไขอยู่น้า ใจเย็นๆน้า อารมณ์เสียมากๆ เดี๋ยวแก่เร็วนะ';
	$pingresult = 'เค้าไม่ว่างอ่ะตัวเอง';
	if (substr($server['timer'], 0, 10) == date("Y-m-d")) {
		//lastchangedatetime == datenow, tell only time
		$previous_time = substr($server['timer'], 11);
		$previous_time = str_replace(':', '.', $previous_time);
		$pingresult = 'ล่าสุดเมื่อเวลา ' . $previous_time . 'น. เซิฟเวอร์ ' . $ip_address . $normally;
	}
	else {
		//lastchangedatetime != datenow, tell date and time
		$previous_date = date("d/m/Y", strtotime(substr($server['timer'], 0, 10)));
		$previous_time = substr($server['timer'], 11);
		$previous_time = str_replace(':', '.', $previous_time);
		$pingresult = 'ล่าสุดเมื่อวันที่ ' . $previous_date . ' เวลา ' . $previous_time . 'น. เซิฟเวอร์ ' . $ip_address . $normally;
	}
	return $pingresult;
}
/**********************************************************************************************************************************/
//Function to insert data to postgresql database to easier than insert data to database by terminal
function InsertDataToDB() {

	$db = pg_connect("host=ec2-54-243-187-133.compute-1.amazonaws.com 
					port=5432 
					dbname=dfusod038c3j35 
					user=mmbbbssobrmqjs 
					password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa");
	$t = 'text';
	$result = pg_query($db, "INSERT INTO tbhlinebotans ($t, type) VALUES 
							('ไม่รู้จ้า', '10')
							,('ไม่รู้สิจ๊ะ', '10')
							,('ก็ไม่รู้สินะ', '10')	
							,('ไม่รู้ว้อยยย', '10')
							,('เค้าขอโทษ เค้าไม่รู้ T_T', '10')
							,('จะถามคนอื่นทำไมหล่ะ ลองถามใจตัวเธอเองดูสิ', '10')						
							;");			

	// $curr_temperature = 24;
	// $result = pg_query($db, "UPDATE tbhlinebottemploc 
	// 						SET temperature = $curr_temperature
	// 						WHERE location = 'ITSD Room'");		
}