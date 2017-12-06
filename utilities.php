<?php
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
12. Timer mode for answer when's question.
*******************************************************************/
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
	if ($mood > 12) {
		$notepad = file('text/reply.txt');
		$resultreply = 'ถ้าคุณขับรถบรรทุกคนไป 43 คน เพื่อไปเชียงใหม่ แต่ระหว่างทางคุณรับคนอีก 7 คน เพื่อไปส่งที่ภูเก็ต ถามว่าคนขับชื่ออะไรระหว่าง ควาย กับ หมา?';
		if (count($notepad) > 0) {
			$resultreply = $notepad[rand(1, count($notepad))];
			$resultreply = substr($resultreply, 0, strlen($resultreply)-1);
		}
		return $resultreply;
	}

	$db = new PDO($GLOBALS['dsn']);
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
	//Trim all space ' '
	$text = str_replace(' ', '', $text);

	$QAArray = QuestionWordFromDB();
	foreach ($QAArray as $keyitems) {
		if ($keyitems['type'] == 4) {
			if (startsWith($text, $keyitems['text']) || endsWith($text, $keyitems['text'])) {
				return $keyitems['type'];
			}
		}
		else if ($keyitems['type'] == 5) {
			if (strpos($text, 'สถานะ') !== false || strpos($text, 'สเตตัส') !== false || strpos($text, 'status') !== false) {
				return 8;
			}
			else if (startsWith($text, $keyitems['text']) || endsWith($text, $keyitems['text'])) {
				return $keyitems['type'];
			} 
		}  
		else if ($keyitems['type'] == 8 || $keyitems['type'] == 9) {
			if ($keyitems['type'] == 8 && strpos($text, 'อุณหภูมิ') !== false) {	
				return 7;
			}
			else if (strpos($text, $keyitems['text']) !== false) {
				foreach ($QAArray as $item) {
					if (endsWith($text, $item['text']) && $item['type'] <= 7) {
						return $item['type'];
					}
				}
				return $keyitems['type'];
			}
		}
		else if (endsWith($text, $keyitems['text'])) {
			if (($keyitems['type'] == 1 && (strpos($text, 'ล่ม') !== false || strpos($text, 'เจ๊ง') !== false || strpos($text, 'เดี้ยง') !== false ||
										    strpos($text, 'พัง') !== false || strpos($text, 'ดับ') !== false || strpos($text, 'ปกติ') !== false))) {
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
	$db = new PDO($GLOBALS['dsn']);
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
	$db = new PDO($GLOBALS['dsn']);
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
	$db = new PDO($GLOBALS['dsn']);
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
		if ($ip_req < 1000000) {
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
		else if (strpos(strtolower($text), strtolower($server['name'])) !== false && 
			($server['name'] != 'PrinterServerS' && $server['name'] != 'SERVER')) {		
			$ip_addr['IsChecked'] = true;
			$ip_addr['ip_addr'] = $server['ip'];
			break;
		}
	}

	return $ip_addr;
}
/**********************************************************************************************************************************/
function GetPingAnswer($ip_address) {
	$db = new PDO($GLOBALS['dsn']);
	$query = "SELECT status, lastchangedatetime AT TI"
							."ME ZONE 'UTC+7' as lastchangedatetime FROM tbhlinebotserv WHERE ip_addr = '$ip_address'";
	$result = $db->query($query);

	$server = array();
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$server['status'] = htmlspecialchars($row["status"]);
		$server['timer'] = substr(htmlspecialchars($row["lastchangedatetime"]), 0, 16);
	}
	$result->closeCursor();

	$normally = ' จะเป็นยังไงก็ไม่รู้สิ ช่างมัน เช๊อะ!';
	switch ($server['status']) {
		case 'stable':
			$normally = ' ปกติดีจ้า';
			break;
		case 'warning':
			$normally = ' อาจจะทำงานช้าบ้างนิดหน่อย แต่น่าจะยังสามารถใช้งานได้น๊ะจ๊ะ';
			break;
		case 'danger':
			$normally = ' น่าจะมีปัญหาแล้วหล่ะ กำลังติดต่อผู้เกี่ยวข้องให้แก้ไขอยู่น้า ใจเย็นๆน้า อารมณ์เสียมากๆ เดี๋ยวแก่เร็วนะ';
			break;
		default:
			$normally = ' จะเป็นยังไงก็ไม่รู้สิ ช่างมัน เช๊อะ!';
			break;
	}

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
function GetLocation() {
	$db = new PDO($GLOBALS['dsn']);
	// Maybe use where in title column to change result to array 1 direction, but now just random location.
	$query = 'SELECT id, title, address, latitude, longitude FROM linebotlocation ORDER BY id ASC'; 
	$result = $db->query($query);

	$locations = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $locations[$index] = array();
		$locations[$index]['title'] = htmlspecialchars($row["title"]);
		$locations[$index]['address'] = htmlspecialchars($row["address"]);
		$locations[$index]['latitude'] = htmlspecialchars($row["latitude"]);
		$locations[$index]['longitude'] = htmlspecialchars($row["longitude"]);
		$index = $index + 1;
	}
	$result->closeCursor();

	$randnum = rand(0, $index-1);
	$location = array('title'     => $locations[$randnum]['title'],
					  'address'   => $locations[$randnum]['address'],
					  'latitude'  => $locations[$randnum]['latitude'],
					  'longitude' => $locations[$randnum]['longitude']);

	return $location;
}
/**********************************************************************************************************************************/
function SubEndText($text) {
	if (endsWith($text, 'บ้าง') || endsWith($text, 'อยู่')) {
		$text = substr($text, 0, -12);
	}
	else if (endsWith($text, 'ดี')) {
		$text = substr($text, 0, -6);
	}
	return $text;
}
/**********************************************************************************************************************************/
function IsAvailable($userId) {
	$db = new PDO($GLOBALS['dsn']);
	$query = "SELECT bot_mode FROM tbhlinebotmodchng WHERE user_id = '$userId'"; 
	$result = $db->query($query);

	$botmod = 'trial';
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $botmod = htmlspecialchars($row["bot_mode"]);
	}
	$result->closeCursor();

	return $botmod;
}
/**********************************************************************************************************************************/
function InsertIdToDB($userId) {
	$db = new PDO($GLOBALS['dsn']);
	$query = "SELECT user_id FROM tbhlinebotmodchng WHERE user_id = '$userId'"; 
	$result = $db->query($query);

	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $activate = htmlspecialchars($row["user_id"]);
	}
	$result->closeCursor();

	if (empty($activate)) {
		$db2 = pg_connect($GLOBALS['pgsql_conn']);		
		$results = pg_query($db2, "INSERT INTO tbhlinebotmodchng (user_id) VALUES ('$userId');");	
	}
}
/**********************************************************************************************************************************/
function RegisterMode($text, $userId, $userType) {
	$botname = "@kiki";
	$db = new PDO($GLOBALS['dsn']);
	$query = "SELECT seq FROM tbhlinebotmodchng WHERE user_id = '$userId'"; 
	$result = $db->query($query);

	$stage = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $stage = htmlspecialchars($row["seq"]);
	}
	$result->closeCursor();

	$db2 = pg_connect($GLOBALS['pgsql_conn']);	
	$error = false;
	switch ($stage) {
		case '1':
			# user tell name
			# for infinite loop find empty id to insert in table tbhlinebotmem
			$countable = 1;
			while(true) {
				$query2 = "SELECT id FROM tbhlinebotmem WHERE id = '$countable'"; 
				$result2 = $db->query($query2);

				$curr_val = 0;
				while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
				    $curr_val = htmlspecialchars($row["id"]);
				}
				$result2->closeCursor();

				if ($curr_val == 0 || empty($curr_val)) {
					break;
				}
				$countable = $countable + 1;
			}
			$text = str_replace("ชื่อ", "", $text);
			$text = str_replace("ไอ้", "", $text);
			$text = str_replace("ท่าน", "", $text);
			$text = str_replace("พี่", "", $text);
			$text = str_replace("น้อง", "", $text);
			$text = str_replace("กลุ่ม", "", $text);
			$text = str_replace("ห้อง", "", $text);
			$text = str_replace($botname, "", $text);
			if ($userType != 'user') {
				$text = str_replace(" ", "", $text);
			}
			//$text = str_replace("นามสกุล", "", $text);
			$roomgroup = "@" . $text;
			$results = pg_query($db2, "INSERT INTO tbhlinebotmem (id, user_id, name, linename, position, id_type) 
									   VALUES ('$countable', '$userId', '$text', '$roomgroup', 'member', '$userType');");
			if ($userType == 'user') {
				$toggle = 2;
				$str = "คุณ$text กรุณาระบุชื่อไลน์ของคุณด้วยด้วยจ้า (เช่นของผมคือ @kiki อย่าลืมใส่เครื่องหมาย @ นะ)";
			}
			else if ($userType == 'group') {
				$toggle = 5;
				$str = "ชื่อกลุ่มของคุณคือ $text\nยืนยันการลงทะเบียนใช้งาน Line Chat Bot เต็มรูปแบบใช่หรือไม่";
			}
			else if ($userType == 'room') {
				$toggle = 5;
				$str = "ชื่อห้องของคุณคือ $text\nยืนยันการลงทะเบียนใช้งาน Line Chat Bot เต็มรูปแบบใช่หรือไม่";
			}
			else {
				$error = true;
				$str = "ขออภัยขณะนี้ระบบลงทะเบียนมีปัญหา ไว้มาลงทะเบียนใหม่ทีหลังน๊ะจ๊ะคนดีดนเก่งของพี่จุ๊บๆ";
			}
			break;
		case '2':
			# user tell line name
			if (startsWith($text, '@')) {
				$query3 = "SELECT name FROM tbhlinebotmem WHERE user_id = '$userId'"; 
				$result3 = $db->query($query3);

				$name = "";
				while ($row = $result3->fetch(PDO::FETCH_ASSOC)) {
				    $name = htmlspecialchars($row["name"]);
				}
				$result3->closeCursor();

				$results = pg_query($db2, "UPDATE tbhlinebotmem SET linename = '$text' WHERE user_id = '$userId';");
				$toggle = 3;
				$str = "ชื่อไลน์ของคุณ" . $name . "คือ $text\nกรุณาระบุเพศด้วยจ้า (ชาย / หญิง)";
			}
			else {
				$error = true;
				$str = "ก็บอกให้ใส่เครื่องหมาย @ ด้วยไง ปัดโธ่ ไปเริ่มกรอกใหม่ตั้งแต่ต้นเลยไป๊!";
			}
			break;		
		case '3':
			# user tell gender
			if ((strpos($text, 'หญิง') !== false) || (strpos(strtolower($text), 'female') !== false)) {
				$results = pg_query($db2, "UPDATE tbhlinebotmem SET gender = 'F' WHERE user_id = '$userId';");
				$toggle = 4;
				$str = "คุณผู้หญิง กรุณาระบุวันเดือนปีเกิด (ในรูปแบบ dd/mm/yyyy เช่น 01/01/1900) ด้วยจ้า";
			}
			else if ((strpos($text, 'ชาย') !== false) || (strpos(strtolower($text), 'male') !== false)) {
				$results = pg_query($db2, "UPDATE tbhlinebotmem SET gender = 'M' WHERE user_id = '$userId';");
				$toggle = 4;
				$str = "คุณผู้ชาย กรุณาระบุวันเดือนปีเกิด (ในรูปแบบ(ค.ศ.) dd/mm/yyyy เช่น 01/01/1900) ด้วยจ้า";
			} 
			else {
				$error = true;
				$str = "ก็ให้ใส่แค่ ชาย หรือ หญิง ไง แล้วนี่กรอกอะไรมา ไปเริ่มกรอกใหม่เลยละกัน!";
			}
			break;
		case '4':
			# user tell date of birth
			preg_match_all("!\d+!", $text, $matches);
			if (count($matches[0]) == 3) {
				$bd = $matches[0][2] . '-' . $matches[0][1] . '-' . $matches[0][0] . ' 00:00:00';
				$bd2 = $matches[0][0] . '/' . $matches[0][1] . '/' . $matches[0][2];
				if (($bd < date("Y-m-d H:i:s")) && ($bd > date("Y-m-d H:i:s", strtotime("-150 Years")))) {
					$results = pg_query($db2, "UPDATE tbhlinebotmem SET date_of_birth = '$bd' WHERE user_id = '$userId';");

					if (checkdate($matches[0][1], $matches[0][0], $matches[0][2])) {
						$toggle = 5;
						$str = "คุณเกิดวันที่ $bd2\nยืนยันการลงทะเบียนใช้งาน Line Chat Bot เต็มรูปแบบใช่หรือไม่";
					}
					else {
						$error = true;
						$str = "ฮั่นแน่! กรอกวันที่มั่วๆมาหน่ะสิคิดว่า...ไม่รู้หรอ ไปกรอกเริ่มใหม่ตั้งแต่ต้นน๊ะจ๊ะ";
					}

				}
				else {
					$error = true;
					$str = "อายุคูณไม่ได้อยู่ในช่วง 150 ปีที่ผ่านมา คุณเป็นใครกันแน่เนี่ยยยย!";	
				}
			}
			else {
				$error = true;
				$str = "ก็บอกให้กรอกวันที่ในรูปแบบ(ค.ศ.) dd/mm/yyyy เช่น 01/01/1900 ไง ไปเริ่มกรอกใหม่ตั้งแต่ต้นเลยไป๊!";
			}
			break;
		case '5':
			# acception by user
			if (strpos($text, 'ไม่') !== false) {
				$error = true;
				$str = "ว่างหรอ?";
			}
			else if ((strpos($text, 'ใช่') !== false) || (strpos(strtolower($text), 'yes') !== false) || 
					 (strpos(strtolower($text), 'yeah') !== false) || (strpos(strtolower($text), 'y') !== false) || 
					 (strpos(strtolower($text), 'absolute') !== false) || (strpos(strtolower($text), 'whynot') !== false) || 
					 (strpos(strtolower($text), 'sure') !== false)) {
				IsAcceptingMember($userId);
				$toggle = 6;
				$str = "ขอคิดดูก่อนนะว่าจะรับดีมั้ยน้า แล้วเดี๋ยวจะมาบอกทีหลังนะ";
			}
			else {
				$error = true;
				$str = "ว่างหรอ?";
			}
			break;
		case '6':
			$toggle = 6;
			$str = "คำขอใช้งานแชทบอทของคุณกำลังรออนุมัติ กรุณารอสักครู่น๊ะจ๊ะ";
			break;
		default:
			$error = true;
			$str = "ขออภัยขณะนี้ระบบลงทะเบียนมีปัญหา ไว้มาลงทะเบียนใหม่ทีหลังน๊ะจ๊ะคนดีดนเก่งของพี่จุ๊บๆ";
	}

	if ($error) {
		$results = pg_query($db2, "DELETE FROM tbhlinebotmem WHERE user_id = '$userId';");
		$results = pg_query($db2, "UPDATE tbhlinebotmodchng SET bot_mode = 'trial' WHERE user_id = '$userId';");
		$toggle = 0;
	}
	$result_again = pg_query($db2, "UPDATE tbhlinebotmodchng SET seq = '$toggle' WHERE user_id = '$userId';");

	return $str;
}
/**********************************************************************************************************************************/
function IsAcceptingMember($userId) {
	$db = new PDO($GLOBALS['dsn']);
	$query = "SELECT name, linename, gender, date_of_birth, id_type FROM tbhlinebotmem WHERE user_id = '$userId'"; 
	$result = $db->query($query);

	$new_member = array();
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $new_member['name'] = htmlspecialchars($row["name"]);
	    $new_member['linename'] = htmlspecialchars($row["linename"]);
	    $new_member['gender'] = htmlspecialchars($row["gender"]);
	    $new_member['bd'] = htmlspecialchars($row["date_of_birth"]);
	    $new_member['type'] = htmlspecialchars($row["id_type"]);
	}
	$result->closeCursor();

	MemberConfirmation($new_member);

	$query2 = "SELECT user_id FROM tbhlinebotmem WHERE position = 'admin'"; 
	$result2 = $db->query($query2);

	$admin = array();
	$index = 0;
	while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
	    $admin[$index] = htmlspecialchars($row["user_id"]);
	    $index = $index + 1;
	}
	$result2->closeCursor();

	$db2 = pg_connect($GLOBALS['pgsql_conn']);
	$awaitadmin = "UPDATE tbhlinebotmodchng SET bot_mode = 'await' WHERE ";
	foreach ($admin as $adm) {
		$awaitadmin .= "user_id = '$adm' or ";
	}
	$awaitadmin = substr($awaitadmin, 0, -3);
	$awaitadmin .= ";";
	$result3 = pg_query($db2, $awaitadmin);
}
/**********************************************************************************************************************************/
function MemberConfirmation($arrayData) {
	$access_token = 'CFecc4UnPdpCUxVk2VuTlf7ANCYHbCpaxYltjR/z15zMJ/KzsPIVrp4tCql4xmQYr8qgJSZ6oitEZ0/PKH+FpdneucSfPgjTP03mQ5KRSKqYT93fEEvGDqOUxJ/SBoS3oTXcJaRSxlPVBWxH+8PWxAdB04t89/1O/w1cDnyilFU=';

	$confirm = "มีผู้ต้องการใช้งาน Line Chat Bot อย่างเต็มระบบ\nชื่อ : " . $arrayData['name']; 
	$confirm .= "\nชื่อไลน์ : " . $arrayData['linename'];
	if (!empty($arrayData['gender']) && !empty($arrayData['bd'])) {
		$confirm .= "\nเพศ : " . $arrayData['gender'] . "\nวันเกิด : " . $arrayData['bd'];
	}
	$confirm .= "\nประเภท : " . $arrayData['type'] . "\nต้องการให้คนนี้สามารถใช้งานได้เต็มรูปแบบหรือไม่?";

	$messages = [						
		'type' => 'text',
		'text' => $confirm
	];

	$db = new PDO($GLOBALS['dsn']);
	$query = "SELECT user_id FROM tbhlinebotmem WHERE position = 'admin'"; 
	$result = $db->query($query);

	$admin = array();
	$order = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $admin[$order] = htmlspecialchars($row["user_id"]);
	    $order = $order + 1;
	}
	$result->closeCursor();

	//have to query admin from table to accept register member request
	//$admin = 'Ua492767fd96449cd8a857b101dbdbcce';	//ball
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
function ReturnAllowToAdmin() {
	$db = new PDO($GLOBALS['dsn']);

	$query = "SELECT user_id FROM tbhlinebotmem WHERE position = 'admin'"; 
	$result = $db->query($query);

	$admin = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $admin[$index] = htmlspecialchars($row["user_id"]);
	    $index = $index + 1;
	}
	$result->closeCursor();

	$db2 = pg_connect($GLOBALS['pgsql_conn']);
	$awaitadmin = "UPDATE tbhlinebotmodchng SET bot_mode = 'allow' WHERE ";
	foreach ($admin as $adm) {
		$awaitadmin .= "user_id = '$adm' OR ";
	}
	$awaitadmin = substr($awaitadmin, 0, -4);
	$awaitadmin .= ";";
	$result2 = pg_query($db2, $awaitadmin);
}
/**********************************************************************************************************************************/
function DeleteIdRow($text) {
	$db = new PDO($GLOBALS['dsn']);
	$query = "SELECT user_id, name, linename FROM tbhlinebotmem ORDER BY id ASC"; 
	$result = $db->query($query);

	$del_user = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$del_user[$index] = array();
	    $del_user[$index]['id'] = htmlspecialchars($row["user_id"]);
	    $del_user[$index]['name'] = htmlspecialchars($row["name"]);
	    $del_user[$index]['linename'] = htmlspecialchars($row["linename"]);
	    $index = $index + 1;
	}
	$result->closeCursor();

	foreach ($del_user as $del) {
		if ((strpos($text, $del['linename']) !== false) && (strpos($text, $del['name']) !== false)) {
			$rm = $del['id'];
			$db2 = pg_connect($GLOBALS['pgsql_conn']);
			$result2 = pg_query($db2, "DELETE FROM tbhlinebotmem WHERE user_id = '$rm';");
			$result_again = pg_query($db2, "UPDATE tbhlinebotmodchng SET bot_mode = 'trial', seq = '0' WHERE user_id = '$rm';");
			BotPushAllowAccess($rm, false);
			return "ระบบดำเนินการตามคำอนุมัติเรียบร้อย";
		}
	}

	return "ไม่สามารถจัดการข้อมูลได้ หรืออาจจะไม่มีรายชื่อนี้ กรุณาตรวจสอบ หรือ จัดการกับฐานข้อมูลโดยตรง";
}
/**********************************************************************************************************************************/
function ListWaitRegister() { //<-- current bugged this function.
	$db = new PDO($GLOBALS['dsn']);

	$query = "SELECT user_id FROM tbhlinebotmodchng WHERE bot_mode = 'regis'"; 
	$result = $db->query($query);

	$regis = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $regis[$index] = htmlspecialchars($row["user_id"]);
	    $index = $index + 1;
	}
	$result->closeCursor();

	if (empty($regis)) {
		return "ไม่มีรายชื่อขอเข้าใช้งานเต็มรูปแบบตกค้าง";
	}

	$query2 = "SELECT name, linename FROM tbhlinebotmem WHERE ";
	foreach ($regis as $item) {
		$query2 .= "user_id = '$item' OR ";
	}
	$query2 = substr($query2, 0, -4);
	$result2 = $db->query($query2);

	$sum = array();
	$seq = 0;
	while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
		$sum[$seq] = array();
	    $sum[$seq]['linename'] = htmlspecialchars($row["linename"]);
	    $sum[$seq]['name'] = htmlspecialchars($row["name"]);
	    $seq = $seq + 1;
	}
	$result2->closeCursor();

	$ret = "ไม่มีรายชื่อขอเข้าใช้งานเต็มรูปแบบตกค้าง";
	if (!empty($sum)) {
		$ret = "เหลือผู้ที่รออนุมัติการใช้งานแชทบอทเต็มรูปแบบดังต่อไปนี้\n";
		foreach ($sum as $key) {
			$ret .= $key['linename'] . " " . $key['name'] . "\n";
		}
		$ret = substr($ret, 0, -1);
	}
	return $ret;
}
/**********************************************************************************************************************************/
function CheckRegis($userId) {
	$db = new PDO($GLOBALS['dsn']);

	$query = "SELECT bot_mode FROM tbhlinebotmodchng WHERE user_id = '$userId'"; 
	$result = $db->query($query);

	$bot_mod = "trial";
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $bot_mod = htmlspecialchars($row["bot_mode"]);
	}
	$result->closeCursor();
	return $bot_mod;
}
/**********************************************************************************************************************************/
function SetRegisterSeq($userId) {
	$db = pg_connect($GLOBALS['pgsql_conn']);
	$result = pg_query($db, "UPDATE tbhlinebotmodchng SET bot_mode = 'regis', seq = '1' WHERE user_id = '$userId';");
}
/**********************************************************************************************************************************/
function ConfirmRowUserMember($text) {
	$db = new PDO($GLOBALS['dsn']);

	$query = "SELECT user_id, name, linename FROM tbhlinebotmem ORDER BY id ASC"; 
	$result = $db->query($query);

	$awaitmem = array();
	$order = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $awaitmem[$order]["id"] = htmlspecialchars($row["user_id"]);
	    $awaitmem[$order]["linename"] = htmlspecialchars($row["linename"]);
	    $awaitmem[$order]["name"] = htmlspecialchars($row["name"]);
	    $order = $order + 1;
	}
	$result->closeCursor();
	foreach ($awaitmem as $awaitusr) {
		if ((strpos($text, $awaitusr['linename']) !== false) || (strpos($text, $awaitusr['name']) !== false)) {
			$usrid = $awaitusr["id"];
			$db2 = pg_connect($GLOBALS['pgsql_conn']);
			$result2 = pg_query($db2, "UPDATE tbhlinebotmodchng SET bot_mode = 'allow', seq = '0' WHERE user_id = '$usrid';");
		}
	}
	BotPushAllowAccess($usrid, true);
	return "ระบบดำเนินการตามคำอนุมัติเรียบร้อย";
}
/**********************************************************************************************************************************/
function BotPushAllowAccess($memberId, $allow) {
	$access_token = 'CFecc4UnPdpCUxVk2VuTlf7ANCYHbCpaxYltjR/z15zMJ/KzsPIVrp4tCql4xmQYr8qgJSZ6oitEZ0/PKH+FpdneucSfPgjTP03mQ5KRSKqYT93fEEvGDqOUxJ/SBoS3oTXcJaRSxlPVBWxH+8PWxAdB04t89/1O/w1cDnyilFU=';

	if ($allow) {
		$tx = "คำขอใช้งาน Line Chat Bot ของคุณได้รับการอนุญาตแล้ว ยินดีต้อนรับสู่การใช้งาน Line Chat Bot อย่างเต็มรูปแบบนะคร้าบบบบบ";
	}
	else {
		$tx = "คำขอใช้งาน Line Chat Bot ของคุณถูกปฏิเสธ ไม่ต้องเศร้าไปนะ อย่าไปแอบร้องไห้ในห้องน้ำ อย่าสิ้นคิดไปติดยา อย่าทำร้ายตัวเอง ไว้ลองใหม่คราวหน้าละกันเนาะ";
	}

	$messages = [						
		'type' => 'text',
		'text' => $tx
	];

	// Make a POST Request to Messaging API to push to sender
	$url = 'https://api.line.me/v2/bot/message/push';
	$data = [
		'to' => $memberId,
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
//Function to insert data to postgresql database to easier than insert data to database by terminal
function InsertDataToDB($userId, $userType) {
	$db = pg_connect($GLOBALS['pgsql_conn']);		

	//now tbhlinebotwmode id 37-39 is empty
	// $t = 'text';
	$result = pg_query($db, "INSERT INTO tbhlinebotmem (user_id, name, linename, gender, date_of_birth, position, id_type) VALUES 
						('$userId', 'บอล', '@Ball Sarayuth', 'M', '1990-10-28 00:00:00', 'admin', '$userType')						
						;");//,('คืนนี้แหล่ะ อยากได้กี่ครั้งหล่ะ', '12')

	// $result = pg_query($db, "UPDATE tbhlinebotmodchng
	// 						SET bot_mode = 'allow'
	// 						WHERE id = '1'");		
}