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
13. Greeting sentences for response to user.
14. Wishing words for user's birthday.
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

$access_token = 'CFecc4UnPdpCUxVk2VuTlf7ANCYHbCpaxYltjR/z15zMJ/KzsPIVrp4tCql4xmQYr8qgJSZ6oitEZ0/PKH+FpdneucSfPgjTP03mQ5KRSKqYT93fEEvGDqOUxJ/SBoS3oTXcJaRSxlPVBWxH+8PWxAdB04t89/1O/w1cDnyilFU=';

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
	if ($mood > 14) {
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
	    $index += 1;
	}
	$result->closeCursor();
	//$reply = array_filter($reply);

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
	$temp = str_replace(' ', '', $text);

	$QAArray = QuestionWordFromDB();
	foreach ($QAArray as $keyitems) {
		if ($keyitems['type'] == 4) {
			//if (startsWith($temp, $keyitems['text']) || endsWith($temp, $keyitems['text'])) {
			if (strpos($text, $keyitems['text']) !== false) {
				return $keyitems['type'];
			}
		}
		else if ($keyitems['type'] == 5) {
			if (strpos($text, 'สถานะ') !== false || strpos($text, 'สเตตัส') !== false || strpos($text, 'status') !== false) {
				return 8;
			}
			else if (startsWith($temp, $keyitems['text']) || endsWith($temp, $keyitems['text'])) {
				return $keyitems['type'];
			} 
		}  
		else if ($keyitems['type'] == 8 || $keyitems['type'] == 9) {
			if ($keyitems['type'] == 8 && ((strpos($text, 'อุณหภูมิ') !== false) || (strpos($text, 'ความชื้น') !== false))) {	
				return 7;
			}
			else if (strpos($text, $keyitems['text']) !== false) {
				return $keyitems['type'];
			}
		}
		else if (endsWith($temp, $keyitems['text'])) {
			if (($keyitems['type'] == 1 && (strpos($text, 'ล่ม') !== false || strpos($text, 'เจ๊ง') !== false || strpos($text, 'เดี้ยง') !== false ||
										    strpos($text, 'พัง') !== false || strpos($text, 'ดับ') !== false))) {
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
	$query = "SELECT id, questiontext, questiontype FROM tbhlinebotwmode ORDER BY id ASC";
	$result = $db->query($query);

	$qwords = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $qwords[$index] = array();
		$qwords[$index]['text'] = htmlspecialchars($row["questiontext"]);
		$qwords[$index]['type'] = htmlspecialchars($row["questiontype"]);
		$index += 1;
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
		$index += 1;
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
		$query_locnametemp = "SELECT temperature, humidity, lastchangedatetime AT TI"
							."ME ZONE 'UTC+7' as lastchangedatetime FROM tbhlinebottemploc WHERE id = $curr_place";
		$results = $db->query($query_locnametemp);
		$last_temp = array();
		while ($row = $results->fetch(PDO::FETCH_ASSOC)) {  
			$last_temp['temp'] = htmlspecialchars($row["temperature"]);
			$last_temp['humid'] = htmlspecialchars($row["humidity"]);
			$last_temp['datetime'] = substr(htmlspecialchars($row["lastchangedatetime"]), 0, 16);
		}
		$results->closeCursor();

		if (strpos($text, 'อุณหภูมิ') !== false) {
			if (!empty($last_temp['temp'])) {
				$cur_result = $last_temp['temp'] . ' องศาเซลเซียส จ้า';
			}
			else {
				return 'ขณะนี้ยังไม่ได้ติดตั้งระบบวัดอุณหภูมิที่' . $curr_locname . 'เลยจ้า ไว้มาตรวจสอบใหม่ทีหลังนะ';
			}
		}
		else if (strpos($text, 'ความชื้น') !== false) {
			if (!empty($last_temp['humid'])) {
				$cur_result = $last_temp['humidity'] . '% จ้า';
			}
			else {
				return 'ขณะนี้ยังไม่ได้ติดตั้งระบบวัดความชื้นที่' . $curr_locname . 'เลยจ้า ไว้มาตรวจสอบใหม่ทีหลังนะ';
			}
		}
		else {
			return 'อยากรู้อะไรก็เดินไดูเองเลยสิจ๊ะ';
		}

		if (substr($last_temp['datetime'], 0, 10) == date("Y-m-d")) {
			//lastchangedatetime == datenow, tell only time
			$previous_time = substr($last_temp['datetime'], 11);
			$previous_time = str_replace(':', '.', $previous_time);
			$tempresult = 'ล่าสุดเมื่อเวลา ' . $previous_time . 'น. อุณหภูมิที่' . $curr_locname . 'เท่ากับ ' . $cur_result;
		}
		else {
			//lastchangedatetime != datenow, tell date and time
			$previous_date = date("d/m/Y", strtotime(substr($last_temp['datetime'], 0, 10)));
			$previous_time = substr($last_temp['datetime'], 11);
			$previous_time = str_replace(':', '.', $previous_time);
			$tempresult = 'ล่าสุดเมื่อวันที่ ' . $previous_date . ' เวลา ' . $previous_time . 'น. อุณหภูมิที่' . $curr_locname . 'เท่ากับ ' . $cur_result;
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
		$index += 1;
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
		$index += 1;
	}
	$result->closeCursor();

	$randnum = rand(0, $index-2); //rand(0, $index-1);
	$location = array('title'     => $locations[$randnum]['title'],
					  'address'   => $locations[$randnum]['address'],
					  'latitude'  => $locations[$randnum]['latitude'],
					  'longitude' => $locations[$randnum]['longitude']);

	return $location;
}
/**********************************************************************************************************************************/
function SubEndText($text) {
	if (startsWith($text, 'มี')) {
		$text = substr($text, 6);
	}
	if (endsWith($text, 'บ้าง') || endsWith($text, 'อยู่')) {
		$text = substr($text, 0, -12);
	}
	else if (endsWith($text, 'ดี') && (!endsWith($text, 'สวัสดี') && !endsWith($text, 'หวัดดี'))) {
		$text = substr($text, 0, -6);
	}
	return $text;
}
/**********************************************************************************************************************************/
function IsAvailable($userId) {
	$db = new PDO($GLOBALS['dsn']);
	$query = "SELECT bot_mode, seq FROM tbhlinebotmodchng WHERE user_id = '$userId'"; 
	$result = $db->query($query);

	$botmod = array();
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $botmod['mode'] = htmlspecialchars($row["bot_mode"]);
	    $botmod['seq'] = htmlspecialchars($row["seq"]);
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
			# accept policy
			if (strpos($text, 'ไม่') !== false) {
				$error = true;
				$str = "ว่างหรอ?";
			}
			else if (strpos($text, 'ยืนยัน') !== false) {
				$toggle = 2;
				if ($userType == 'user') {
					$str = "กรุณาระบุชื่อที่ให้ใช้เรียก (ชื่อเล่นก็ได้นะ)";
				} 
				else if ($userType == 'group') {
					$str = "กรุณาระบุชื่อกลุ่ม";
				}
				else if ($userType == 'room') {
					$str = "กรุณาระบุชื่อห้อง";
				}
				else {
					$str = "ขออภัยขณะนี้ระบบลงทะเบียนมีปัญหา ไว้มาลงทะเบียนใหม่ทีหลังน๊ะจ๊ะคนดีดนเก่งของพี่จุ๊บๆ";
				}
			}
			else if (strpos($text, 'ยกเลิก') !== false) { 
				$error = true;
				$str = "เสียใจจัง แต่ไม่เป็นไร ไว้มาสมัครใหม่ทีหลังก็ได้นะ";
			}	
			else {
				$error = true;
				$str = "ว่างหรอ?";
			}
			break;
		case '2':
			# user tell name
			# for infinite loop find empty id to insert in table tbhlinebotmem
			$countable = 1;
			while(true) {
				$query2 = "SELECT id FROM tbhlinebotmem WHERE id = '$countable'"; 
				$result2 = $db->query($query2);

				$curr_val = array();
				while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
				    $curr_val = htmlspecialchars($row["id"]);
				}
				$result2->closeCursor();

				if (empty($curr_val)) {
					break;
				}
				$countable += 1;
			}
			$text = str_replace("ชื่อ", "", $text);
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
				$toggle = 3;
				$str = "พี่$text กรุณาระบุชื่อไลน์ของคุณด้วยด้วยจ้า (เช่นของผมคือ @kiki อย่าลืมใส่เครื่องหมาย @ นะ)";
			}
			//Edit to confirmation in accepting register room or group
			else if ($userType == 'group' || $userType == 'room') {
				$result_again = pg_query($db2, "UPDATE tbhlinebotmodchng SET seq = '6' WHERE user_id = '$userId';");
				return ConfirmationsMsg(3, $text, $userType);
			}
			else {
				$error = true;
				$str = "ขออภัยขณะนี้ระบบลงทะเบียนมีปัญหา ไว้มาลงทะเบียนใหม่ทีหลังน๊ะจ๊ะคนดีดนเก่งของพี่จุ๊บๆ";
			}
			break;
		case '3':
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
				$result_again = pg_query($db2, "UPDATE tbhlinebotmodchng SET seq = '4' WHERE user_id = '$userId';");
				$lname = array('linename' => $text, 'name' => $name);
				return ConfirmationsMsg(2, $lname, $userType);
			}
			else {
				$error = true;
				$str = "ก็บอกให้ใส่เครื่องหมาย @ ด้วยไง ปัดโธ่ ไปเริ่มกรอกใหม่ตั้งแต่ต้นเลยไป๊!";
			}
			break;		
		case '4':
			# user tell gender
			$query4 = "SELECT name FROM tbhlinebotmem WHERE user_id = '$userId'"; 
			$result4 = $db->query($query4);

			$name = "";
			while ($row = $result4->fetch(PDO::FETCH_ASSOC)) {
			    $name = htmlspecialchars($row["name"]);
			}
			$result4->closeCursor();

			if (strpos($text, 'หญิง') !== false) {
				$results = pg_query($db2, "UPDATE tbhlinebotmem SET gender = 'F' WHERE user_id = '$userId';");
				$result_again = pg_query($db2, "UPDATE tbhlinebotmodchng SET seq = '5' WHERE user_id = '$userId';");
				$name = is_null($name) ? 'สาว' : $name;
				$name = 'พี่' . $name . 'สุดสวย';
				return ConfirmationsMsg(4, $name, $userType);
			}
			else if (strpos($text, 'ชาย') !== false) {
				$results = pg_query($db2, "UPDATE tbhlinebotmem SET gender = 'M' WHERE user_id = '$userId';");
				$result_again = pg_query($db2, "UPDATE tbhlinebotmodchng SET seq = '5' WHERE user_id = '$userId';");
				$name = is_null($name) ? 'ชาย' : $name;
				$name = 'พี่' . $name . 'สุดหล่อ';
				return ConfirmationsMsg(4, $name, $userType);
			} 
			else {
				$error = true;
				$str = "ก็ให้ใส่แค่ ชาย หรือ หญิง ไง แล้วนี่กรอกอะไรมา ไปเริ่มกรอกใหม่เลยละกัน!";
			}
			break;
		case '5':
			$bd = $text . ' 00:00:00';
			if (($bd < date("Y-m-d H:i:s")) && ($bd > date("Y-m-d H:i:s", strtotime("-150 Years")))) {
				$results = pg_query($db2, "UPDATE tbhlinebotmem SET date_of_birth = '$bd' WHERE user_id = '$userId';");
				$result_again = pg_query($db2, "UPDATE tbhlinebotmodchng SET seq = '6' WHERE user_id = '$userId';");
				$bd2 = date("d/m/Y", strtotime($text));
				return ConfirmationsMsg(3, $bd2, $userType);
			}
			else {
				$error = true;
				$str = "อายุคูณไม่ได้อยู่ในช่วง 150 ปีที่ผ่านมา คุณเป็นใครกันแน่เนี่ยยยย!";	
			}
			break;
		case '6':
			# acception by user
			if (strpos($text, 'ไม่') !== false) {
				$error = true;
				$str = "ว่างหรอ?";
			}
			else if (strpos($text, 'ยืนยัน') !== false) {
				//now error cannot accept register request in group or room here <--------------------------------------------------
				IsAcceptingMember($userId, $userType);
				$toggle = 7;
				$str = "ขอคิดดูก่อนนะว่าจะรับดีมั้ยน้า แล้วเดี๋ยวจะมาบอกทีหลังนะ";
			}
			else if (strpos($text, 'ยกเลิก') !== false) { 
				$error = true;
				$str = "เสียใจจัง แต่ไม่เป็นไร ไว้มาสมัครใหม่ทีหลังก็ได้นะ";
			}
			else {
				$error = true;
				$str = "ว่างหรอ?";
			}
			break;
		case '7':
			$toggle = 7;
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
function IsAcceptingMember($userId, $userType) {
	$db = new PDO($GLOBALS['dsn']);

	$query2 = "SELECT user_id FROM tbhlinebotmem WHERE position = 'admin'"; 
	$result2 = $db->query($query2);

	$admin = array();
	$index = 0;
	while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
	    $admin[$index] = htmlspecialchars($row["user_id"]);
	    $index += 1;
	}
	$result2->closeCursor();

	$db2 = pg_connect($GLOBALS['pgsql_conn']);
	$awaitadmin = "UPDATE tbhlinebotmodchng SET bot_mode = 'await' WHERE ";
	foreach ($admin as $adm) {
		$awaitadmin .= "user_id = '$adm' or ";
		StandardBotPush($adm, ConfirmationsMsg(6, $userId, $userType));
	}
	if (!empty($admin)) {
		$awaitadmin = substr($awaitadmin, 0, -4);
		$awaitadmin .= ";";
	}
	else {
		$awaitadmin .= "id = '0';";
	}
	$result3 = pg_query($db2, $awaitadmin);
}
/**********************************************************************************************************************************/
function DeleteIdRow($text, $adminId) {
	$db = new PDO($GLOBALS['dsn']);
	$query = "SELECT user_id, name, linename FROM tbhlinebotmem WHERE status = 'trial'"; 
	$result = $db->query($query);

	$del_user = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$del_user[$index] = array();
	    $del_user[$index]['id'] = htmlspecialchars($row["user_id"]);
	    $del_user[$index]['name'] = htmlspecialchars($row["name"]);
	    $del_user[$index]['linename'] = htmlspecialchars($row["linename"]);
	    $index += 1;
	}
	$result->closeCursor();
	$waitres = "คำขอใช้งาน Line Chat Bot ของคุณถูกปฏิเสธ ไม่ต้องเศร้าไปนะ อย่าไปแอบร้องไห้ในห้องน้ำ อย่าสิ้นคิดไปติดยา อย่าทำร้ายตัวเอง ไว้ลองใหม่คราวหน้าละกันเนาะ";
	if (strpos($text, 'ผู้ขอใช้งานทั้งหมด') !== false) {
		foreach ($del_user as $del) {
			StandardBotPush($del['id'], BotReplyText($waitres));
		}
		$db2 = pg_connect($GLOBALS['pgsql_conn']);
		$result2 = pg_query($db2, "DELETE FROM tbhlinebotmem WHERE status = 'trial';");
		$result_again = pg_query($db2, "UPDATE tbhlinebotmodchng SET bot_mode = 'trial', seq = '0' WHERE bot_mode = 'regis';");
		AlertOthersAdmin($adminId, false);	
		return "ระบบดำเนินการตามคำอนุมัติเรียบร้อย";
	}
	else {
		foreach ($del_user as $del) {
			if ((strpos($text, $del['linename']) !== false) && (strpos($text, $del['name']) !== false)) {
				$rm = $del['id'];
				$db2 = pg_connect($GLOBALS['pgsql_conn']);
				$result2 = pg_query($db2, "DELETE FROM tbhlinebotmem WHERE user_id = '$rm';");
				$result_again = pg_query($db2, "UPDATE tbhlinebotmodchng SET bot_mode = 'trial', seq = '0' WHERE user_id = '$rm';");
				StandardBotPush($rm, BotReplyText($waitres));
				AlertOthersAdmin($adminId, false, $del);
				return "ระบบดำเนินการตามคำอนุมัติเรียบร้อย";
			}
		}
	}
	return "กรุณาระบุคำอนุมัติในรูปแบบดังต่อไปนี้\n[คำอนุมัติ] [ชื่อไลน์ผู้ขอใช้งาน] [ชื่อผู้ขอใช้งาน]";
}
/**********************************************************************************************************************************/
function ListWaitRegister($userId) {
	$db = new PDO($GLOBALS['dsn']);

	$query = "SELECT user_id FROM tbhlinebotmodchng WHERE bot_mode = 'regis'"; 
	$result = $db->query($query);

	$regis = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $regis[$index] = htmlspecialchars($row["user_id"]);
	    $index += 1;
	}
	$result->closeCursor();

	if (empty($regis)) {
		$query2 = "SELECT user_id FROM tbhlinebotmem WHERE position = 'admin'"; 
		$result2 = $db->query($query2);

		$admin = array();
		$cnt = 0;
		while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
		    $admin[$cnt] = htmlspecialchars($row["user_id"]);
		    $cnt += 1;
		}
		$result2->closeCursor();

		$db2 = pg_connect($GLOBALS['pgsql_conn']);
		$awaitadmin = "UPDATE tbhlinebotmodchng SET bot_mode = 'allow' WHERE ";
		foreach ($admin as $adm) {
			$awaitadmin .= "user_id = '$adm' or ";
		}
		if (!empty($admin)) {
			$awaitadmin = substr($awaitadmin, 0, -4);
			$awaitadmin .= ";";
		}
		else {
			$awaitadmin .= "id = '0';";
		}
		$result3 = pg_query($db2, $awaitadmin);
	}
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
function ConfirmRowUserMember($text, $adminId) {
	$db = new PDO($GLOBALS['dsn']);

	$query = "SELECT user_id, name, linename FROM tbhlinebotmem ORDER BY id ASC"; 
	$result = $db->query($query);

	$awaitmem = array();
	$order = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $awaitmem[$order]["id"] = htmlspecialchars($row["user_id"]);
	    $awaitmem[$order]["linename"] = htmlspecialchars($row["linename"]);
	    $awaitmem[$order]["name"] = htmlspecialchars($row["name"]);
	    $order += 1;
	}
	$result->closeCursor();
	$waitres = "คำขอใช้งาน Line Chat Bot ของคุณได้รับการอนุญาตแล้ว ยินดีต้อนรับสู่การใช้งาน Line Chat Bot อย่างเต็มรูปแบบนะคร้าบบบบบ";
	if (strpos($text, 'ผู้ขอใช้งานทั้งหมด') !== false) {
		$query2 = "SELECT user_id FROM tbhlinebotmem WHERE status = 'trial'";
		$result4 = $db->query($query2);
		$alltrial = array();
		$seq = 0;
		while ($row = $result4->fetch(PDO::FETCH_ASSOC)) {
		    $alltrial[$seq] = htmlspecialchars($row["user_id"]);
		    $seq += 1;
		}
		$result4->closeCursor();
		foreach ($alltrial as $trial) {
			StandardBotPush($trial, BotReplyText($waitres));
		}
		$db2 = pg_connect($GLOBALS['pgsql_conn']);
		$result2 = pg_query($db2, "UPDATE tbhlinebotmem SET status = 'allow' WHERE status = 'trial';");
		$result_again = pg_query($db2, "UPDATE tbhlinebotmodchng SET bot_mode = 'allow', seq = '0' WHERE bot_mode = 'regis';");
		AlertOthersAdmin($adminId, true);	
		return "ระบบดำเนินการตามคำอนุมัติเรียบร้อย";
	}
	else {
		foreach ($awaitmem as $awaitusr) {
			if ((strpos($text, $awaitusr['linename']) !== false) || (strpos($text, $awaitusr['name']) !== false)) {
				$usrid = $awaitusr["id"];
				$db2 = pg_connect($GLOBALS['pgsql_conn']);
				$result2 = pg_query($db2, "UPDATE tbhlinebotmodchng SET bot_mode = 'allow', seq = '0' WHERE user_id = '$usrid';");
				$result3 = pg_query($db2, "UPDATE tbhlinebotmem SET status = 'allow' WHERE user_id = '$usrid';");
				StandardBotPush($usrid, BotReplyText($waitres));
				AlertOthersAdmin($adminId, true, $awaitusr);
				return "ระบบดำเนินการตามคำอนุมัติเรียบร้อย";
			}
		}
	}
	return "กรุณาระบุคำอนุมัติในรูปแบบดังต่อไปนี้\n[คำอนุมัติ] [ชื่อไลน์ผู้ขอใช้งาน] [ชื่อผู้ขอใช้งาน]";
}
/**********************************************************************************************************************************/
function IdentifyUser($userId) {
	$db = new PDO($GLOBALS['dsn']);

	$query = "SELECT name, gender FROM tbhlinebotmem WHERE user_id = '$userId'"; 
	$result = $db->query($query);

	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$name_req = array();
	    $name_req['name'] = htmlspecialchars($row["name"]);
	    $name_req['gender'] = htmlspecialchars($row["gender"]);
	}
	$result->closeCursor();
	if (isset($name_req)) {
		$prefix = array('0' => 'ไอ้', '1' => 'คุณ', '2' => 'พี่', '3' => 'ท่าน', '4' => 'น้อง', '5' => 'ไอ้...', '6' => '');
		$suffix = array('0' => '', '1' => 'คนดี', '2' => 'คนเก่ง', '3' => 'สุด');
		$randend = rand(0, 3);
		if ($randend == 3) {
			if ($name_req['gender'] == 'M') {
				$suffix[$randend] = $suffix[$randend] . 'หล่อ';
			}
			else if ($name_req['gender'] == 'F') {
				$suffix[$randend] = $suffix[$randend] . 'สวย';
			}
			else {
				$suffix[$randend] = '';
			}
		}
		//return $prefix[rand(0, 6)] . $name_req['name'] . $suffix[$randend];
		return 'พี่' . $name_req['name'];
	}
	else {
		return "";
	}
}
/**********************************************************************************************************************************/
function AlertOthersAdmin($adminId, $IsConfirm, $arrayText) {
	$db = new PDO($GLOBALS['dsn']);

	$query = "SELECT user_id FROM tbhlinebotmem WHERE position = 'admin' and user_id != '$adminId'"; 
	$result = $db->query($query);

	$admins = array();
	$seq = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$admins = array();
	    $admins[$seq] = htmlspecialchars($row["user_id"]);
	    $seq += 1;
	}
	$result->closeCursor();

	$query2 = "SELECT name FROM tbhlinebotmem WHERE user_id = '$adminId'"; 
	$result2 = $db->query($query2);

	while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
	    $adm_name = htmlspecialchars($row["name"]);
	}
	$result2->closeCursor();

	$tx = 'มีผู้';
	if (isset($adm_name)) {
		$tx = 'คุณ' . $adm_name;
	}
	if ($IsConfirm) {
		$tx .= 'อนุมัติ';
	}
	else {
		$tx .= 'ปฏิเสธ';
	}
	$tx .= 'คำขอการใช้งาน';
	if (empty($arrayText)) {
		$tx .= 'ทั้งหมดเรียบร้อยแล้ว';
	}
	else {
		$tx .= 'ของ ' . $arrayText['name'] . ' ' . $arrayText['linename'] . ' เรียบร้อยแล้ว';
	}
	foreach ($admins as $adm) {
		StandardBotPush($adm, BotReplyText($tx));
	}
}
/**********************************************************************************************************************************/
function StandardBotPush($userId, $messages) {

	// Make a POST Request to Messaging API to push to sender
	$url = 'https://api.line.me/v2/bot/message/push';
	$data = [
		'to' => $userId,
		'messages' => [$messages],
	];
	$post = json_encode($data);
	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $GLOBALS['access_token']);

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
function ConfirmationsMsg($stack, $userId, $userType) {
	$botname = '@kiki';
	$actions_y = [
		'type' => 'message',
		'label' => 'ยืนยัน',
		'text' => $userType == 'user' ? 'ยืนยัน' : $botname . ' ยืนยัน' 
	];

	$actions_n = [
		'type' => 'message',
		'label' => 'ยกเลิก',
		'text' => $userType == 'user' ? 'ยกเลิก' : $botname . ' ยกเลิก'
	];
	
	switch ($stack) {
		//case 1 to 3 is confirmation template
		case '1':
			$tx = '';
			$policies = file('text/policy.txt');
			foreach ($policies as $policy) {
				$tx .= $policy;
			}
			StandardBotPush($userId, BotReplyText($tx));
			$actions = array($actions_y, $actions_n);
			$msg = 'คุณได้รับทราบข้อตกลงและยืนยันที่จะขอเข้าใช้งานไลน์แชทบอทอย่างเต็มรูปแบบแล้วใช่หรือไม่?';
			$template = [
				'type' => 'confirm',
				'text' => $msg,
				'actions' => $actions
			];
			$messages = [						
				'type' => 'template',
				'altText' => 'คุณได้รับทราบข้อตกลงและยืนยันที่จะขอเข้าใช้งานไลน์แชทบอทอย่างเต็มรูปแบบแล้วใช่หรือไม่?',
				'template' => $template
			];
			break;
		case '2':
			$actions_m = [
				'type' => 'message',
				'label' => 'ชาย',
				'text' => 'ชาย'
			];
			$actions_f = [
				'type' => 'message',
				'label' => 'หญิง',
				'text' => 'หญิง'
			];
			$actions = array($actions_m, $actions_f);
			$msg = "ชื่อไลน์ของพี่" . $userId['name'] . 'คือ' .  $userId['linename'] . "\nกรุณาระบุเพศด้วยจ้า";
			$template = [
				'type' => 'confirm',
				'text' => $msg,
				'actions' => $actions
			];
			$messages = [						
				'type' => 'template',
				'altText' => $msg,
				'template' => $template
			];
			break;
		case '3':
			$actions = array($actions_y, $actions_n);
			if ($userType == 'user') {
				$msg = 'คุณเกิดวันที่';
			}
			else if ($userType == 'group') {
				$msg = 'ชื่อกลุ่มของคุณคือ ';
			}
			else if ($userType = 'room') {
				$msg = 'ชื่อห้องของคุณคือ ';
			}
			else {
				return BotReplyText('เกิดข้อผิดพลาด กรุณาลองใหม่ภายหลังหรือแจ้งผู้จัดทำไลน์แชทบอทด้วยจ้า');
			}
			$msg .= $userId . "\nยืนยันการลงทะเบียนใช้งาน Line Chat Bot เต็มรูปแบบใช่หรือไม่";
			$template = [
				'type' => 'confirm',
				'text' => $msg,
				'actions' => $actions
			];
			$messages = [						
				'type' => 'template',
				'altText' => $msg,
				'template' => $template
			];
			break;
		case '4':
			//datetime picker template
			$actions_d = [
				'type' => 'datetimepicker',
				'label' => 'เลือกวันที่',
				'data' => 'datetimepicker=ok',
				'mode' => 'date'
			];
			$actions_t = [
				'type' => 'postback',
				'label' => 'ยกเลิกการสมัคร',
				'data' => 'ยกเลิก'
			];
			$actions = array($actions_d, $actions_t);
			$template = [
				'type' => 'buttons',
				'thumbnailImageUrl' => 'https://cryptic-harbor-32168.herokuapp.com/images/bdckiki.jpg',
				'imageAspectRatio' => 'rectangle',
				'imageSize' => 'cover',
				'imageBackgroundColor' => '#FFFFFF',
				'title' => 'กรุณาระบุวันเกิด',
				'text' => $userId . 'เกิดวันที่?',
				'actions' => $actions
			];
			$messages = [						
				'type' => 'template',
				'altText' => 'กรุณาระบุวันเกิด',
				'template' => $template
			];
			break;
		case '5':
			//multi buttons menu template
			$db = new PDO($GLOBALS['dsn']);
			$query = "SELECT bot_mode FROM tbhlinebotmodchng WHERE user_id = '$userId'"; 
			$result = $db->query($query);

			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			    $pos = htmlspecialchars($row["bot_mode"]);
			}
			$result->closeCursor();

			$actions_m1 = [
				'type' => 'postback',
				'label' => 'สมัครใช้งานเต็มรูปแบบ',
				'data' => 'เปิดโหมดลงทะเบียนเข้าใช้งาน'
			];
			$actions_m2 = [
				'type' => 'postback',
				'label' => 'รายชื่อผู้รออนุมัติเข้าใช้งาน',
				'data' => 'waitlist'
			];
			$actions_m3 = [
				'type' => 'uri',
				'label' => 'คู่มือการใช้งาน',
				'uri' => 'https://cryptic-harbor-32168.herokuapp.com/manual.html'
			];
			$actions_m4 = [
				'type' => 'postback',
				'label' => 'หมวดสถานที่',
				'data' => 'locationsplaces'
			];
			if ($pos == 'await') {
				$actions = array($actions_m2, $actions_m3);
			}	
			else if ($pos == 'allow') {
				$actions = array($actions_m4, $actions_m3);
			}
			else {
				$actions = array($actions_m1, $actions_m3);
			}
			$template = [
				'type' => 'buttons',
				'thumbnailImageUrl' => 'https://cryptic-harbor-32168.herokuapp.com/images/readingmenu.png',
				'imageAspectRatio' => 'rectangle',
				'imageSize' => 'cover',
				'imageBackgroundColor' => '#FFFFFF',
				'title' => 'เมนูการใช้งาน',
				'text' => 'กรุณาเลือกเมนูการใช้งาน',
				'actions' => $actions
			];
			$messages = [						
				'type' => 'template',
				'altText' => 'กรุณาเลือกเมนูการใช้งาน',
				'template' => $template
			];
			break;
		case '6':
			$db = new PDO($GLOBALS['dsn']);
			$query = "SELECT name, linename, id_type FROM tbhlinebotmem WHERE user_id = '$userId'"; 
			$result = $db->query($query);

			$new_member = array();
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			    $new_member['name'] = htmlspecialchars($row["name"]);
			    $new_member['linename'] = htmlspecialchars($row["linename"]);
			    $new_member['type'] = htmlspecialchars($row["id_type"]);
			}
			$result->closeCursor();
			
			$actions_1 = [
				'type' => 'message',
				'label' => 'อนุมัติ',
				'text' => 'อนุมัติ ' . $new_member['name'] . ' ' . $new_member['linename']
			];
			$actions_2 = [
				'type' => 'message',
				'label' => 'ยกเลิก',
				'text' => 'ยกเลิก ' . $new_member['name'] . ' ' . $new_member['linename']
			];
			$actions_3 = [
				'type' => 'postback',
				'label' => 'รายละเอียด',
				'data' => 'details=' . $userId
			];
			$actions = array($actions_1, $actions_2, $actions_3);

			$msg = "มีผู้ต้องการใช้งาน Line Chat Bot อย่างเต็มระบบ";
			$detail = '';
			if ($new_member['type'] == 'user') {
				$detail = 'คุณ' . $new_member['name'] . ' ' . $new_member['linename'];
			}
			else {
				if ($new_member['type'] == 'group') {
					$detail = 'กลุ่ม';
				}
				else if ($new_member['type'] == 'room') {
					$detail = 'ห้อง';
				}
				else {
					return BotReplyText('เกิดข้อผิดพลาด กรุณาลองใหม่ภายหลังหรือแจ้งผู้จัดทำไลน์แชทบอทด้วยจ้า');
				}
				$detail .= $new_member['name'];
			}
			$detail .= ' ต้องการใช้งาน Line Chat Bot เต็มรูปแบบ?';
			if (strlen($detail) > 160) {
				$detail = 'มีผู้ต้องการใช้งาน Line Chat Bot เต็มรูปแบบ โปรดดูที่รายละเอียด';
			}
			$template = [
				'type' => 'buttons',
				'title' => $msg,
				'text' => $detail,
				'actions' => $actions
			];
			$messages = [						
				'type' => 'template',
				'altText' => $msg,
				'template' => $template
			];
			break;
		case '7':
			$db = new PDO($GLOBALS['dsn']);
			$query = "SELECT user_id FROM tbhlinebotmodchng WHERE seq = '7'"; 
			$result = $db->query($query);

			$temp_id = array();
			$count = 0;
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			    $temp_id[$count] = htmlspecialchars($row["user_id"]);
			    $count += 1;
			}
			$result->closeCursor();

			$query2 = "SELECT user_id, name, linename, id_type FROM tbhlinebotmem WHERE ";
			foreach ($temp_id as $tid) {
				$query2 .= "user_id = '$tid' or ";
			}
			if (!empty($temp_id)) {
				$query2 = substr($query2, 0, -4);
				$query2 .= ';';
			}
			else {
				$query2 .= "id = '0';";
			}
			$result2 = $db->query($query2);

			$temp_member = array();
			$seq = 0;
			while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
				$temp_member[$seq] = array();
				$temp_member[$seq]['id'] = htmlspecialchars($row["user_id"]);
				$temp_member[$seq]['name'] = htmlspecialchars($row["name"]);
				$temp_member[$seq]['line'] = htmlspecialchars($row["linename"]);
				$temp_member[$seq]['type'] = htmlspecialchars($row["id_type"]);
				$seq += 1;
			}
			$result2->closeCursor();

			$actions_array = array();
			$order = 0;
			if (!empty($temp_member)) {
				foreach ($temp_member as $temp_mem) {
					if ($temp_mem['type'] == 'user') {
						$kind = $temp_mem['name'] . ' ' . $temp_mem['line'];
					}
					else if ($temp_mem['type'] == 'group') {
						$kind = 'กลุ่ม' . $temp_mem['name'];
					}
					else if ($temp_mem['type'] == 'room') {
						$kind = 'ห้อง' . $temp_mem['name'];
					}
					else {
						$kind = '';
					}
					$actions_array[$order] = [
						'type'=> 'postback',
						'label' => $kind,
						'data' => 'identify=' . $temp_mem['id']
					];
					$order += 1;
				}
			}

			$actions_ya = [
				'type' => 'message',
				'label' => 'อนุมัติผู้ขอใช้งานทั้งหมด',
				'text' => 'อนุมัติผู้ขอใช้งานทั้งหมด'
			];
			$actions_na = [
				'type' => 'message',
				'label' => 'ปฏิเสธผู้ขอใช้งานทั้งหมด',
				'text' => 'ปฏิเสธผู้ขอใช้งานทั้งหมด'
			];

			if ($order == 0) {
				$actions = array($actions_ya, $actions_na);
			}
			else {
				$actions = array($actions_ya);
				foreach ($actions_array as $act_a) {
					array_push($actions, $act_a);
				}
				array_push($actions, $actions_na);
			}

			$msg = "รายชื่อผู้ขอใช้งาน Line Chat Bot";
			$detail = "กดเพื่อดูรายละเอียด ($order รายชื่อ)";
			$template = [
				'type' => 'buttons',
				'title' => $msg,
				'text' => $detail,
				'actions' => $actions
			];
			$messages = [						
				'type' => 'template',
				'altText' => $msg,
				'template' => $template
			];
			break;
		case '8':
			// $db = new PDO($GLOBALS['dsn']);
			// $query = "SELECT location FROM tbhlinebotmodchng ORDER BY id ASC"; 
			// $result = $db->query($query);

			// $locate = array();
			// $index = 0;
			// while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			//     $locate[$index] = htmlspecialchars($row["location"]);
			//     $index += 1;
			// }
			// $result->closeCursor();

			$places = array('0' => 'ห้องโปรแกรมเมอร์', '1' => 'ห้องเซิฟเวอร์');
			$counter = 0;
			foreach ($places as $place) {
				if ($userType == 'user') {
					$botname = '';
				}
				else {
					$botname = $botname . ' ';
				}
				$actions_temp[$counter] = [
					'type' => 'message',
					'label' => 'อุณหภูมิ',
					'text' => $botname . 'อุณหภูมิที่' . $place . 'เท่ากับเท่าไร'
				];
				$actions_humid[$counter] = [
					'type' => 'message',
					'label' => 'ความชื้น',
					'text' => $botname . 'ความชื้นที่' . $place . 'เท่ากับเท่าไร'
				];
				$actions[$counter] = array($actions_temp[$counter], $actions_humid[$counter]);
				$counter += 1;
			}

			$sequence = 0;
			foreach ($places as $place) { 
				$pic_link = "https://cryptic-harbor-32168.herokuapp.com/images/location-" . ($sequence + 1) . ".jpg";
				$columns[$sequence] = [
					'thumbnailImageUrl' => $pic_link,
					'imageBackgroundColor' => "#FFFFFF",
					'title' => $place,
					'text' => 'อาคาร 4 ชั้น 3',
					'actions' => $actions[$sequence]
				];
				$sequence += 1;
			}

			$template = [
				'type' => 'carousel',
				'columns' => $columns
			];
			$messages = [						
				'type' => 'template',
				'altText' => 'นี่คือสถานที่ต่าง ๆ ในฐานข้อมูลจ้า',
				'template' => $template
			];
			break;
		default:
			$messages = BotReplyText('เกิดข้อผิดพลาด กรุณาลองใหม่ภายหลังหรือแจ้งผู้จัดทำไลน์แชทบอทด้วยจ้า');
			break;
	}
	return $messages;
}
/**********************************************************************************************************************************/
function BotReplyText($message) {
	$messages = [						
		'type' => 'text',
		'text' => $message
	];
	return $messages;
}
/**********************************************************************************************************************************/
function GetDetailsMember($userId) {
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

	$confirm = "ชื่อ : " . $new_member['name'] . "\nชื่อไลน์ : " . $new_member['linename'];
	if (!empty($new_member['gender']) && !empty($new_member['bd'])) {
		$confirm .= "\nเพศ : " . $new_member['gender'] == 'M' ? 'ชาย' : 'หญิง' . "\nวันเกิด : " . substr($new_member['bd'], 0, 10);
	}
	$confirm .= "\nประเภท : " . $new_member['type'];
	return $confirm;
}
/**********************************************************************************************************************************/
function EndJokeQuestion($text, $userId) {
	$botname = 'Kiki';
	$db = new PDO($GLOBALS['dsn']);
	$query = "SELECT seq FROM tbhlinebotmodchng WHERE user_id = '$userId' AND bot_mode = 'joke'"; 
	$result = $db->query($query);

	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $seq = htmlspecialchars($row["seq"]);
	}
	$result->closeCursor();
	
	if (isset($seq)) {
		$query2 = "SELECT answer FROM tbhlinebotjokeq WHERE id = '$seq'";
		$result2 = $db->query($query2);

		while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
			$joke_ans = htmlspecialchars($row['answer']);
		}
		$result2->closeCursor();

		if (isset($joke_ans)) {
			$soundeff = array('0' => 'ผ่ามๆๆๆพ๊ามมมมม!', '1' => 'เฉียบบบ!', '2' => '', 
							  '3' => 'บู่ววววววว', '4' => 'ว๊ายยย',
							  '5' => '5555555555555555', '6' => 'ถถถถถถถถถถถถถถถถถถถ', '7' => 'วั๊ยๆๆๆๆๆๆๆๆๆๆๆ');
			//$text = str_replace(' ', '', $text);
			if (strpos($text, $joke_ans) !== false) {
				$tx = "ถูกต้องนะคร๊าบบบบบ\nเฉลย " . $joke_ans . $soundeff[rand(0, 2)];
				$pass = true;
			}
			else if (strpos($text, 'ไม่') !== false) {
				$tx = $soundeff[rand(5, 7)];
			}
			else if (strpos($text, 'ยอม') !== false) {
				$tx = 'เฉลย ' . $joke_ans . $soundeff[rand(0, 2)];
				$pass = true;
			}
			else if (strpos($text, 'เฉลย') !== false) {
				$tx = 'ลองขอร้องว่า "คุณ' . $botname . '(ครับ/ค่ะ) (ผม/ฉัน)ขอยอมแพ้แล้ว(ครับ/ค่ะ) สติปัญญาของ(ผม/ฉัน)คงไม่อาจสามารถทัดเทียมคุณ' . $botname . 'ได้" ให้ดูหน่อยสิ';
			}
			else {
				$sx = rand(0, 10000) % 2 == 0 ? 'จ๊ะ' : 'จ้า';
				$tx = $soundeff[rand(2, 4)] . ' ผิดนะ' . $sx . ' ' . $soundeff[rand(5, 7)];
			}

			if (isset($pass) && $pass) {
				$db2 = pg_connect($GLOBALS['pgsql_conn']);
				$result3 = pg_query($db2, "UPDATE tbhlinebotmodchng SET bot_mode = 'allow', seq = '0' where user_id = '$userId'");
			}

			return BotReplyText($tx);
		}
		else {
			return BotReplyText('เกิดข้อผิดพลาด กรุณาลองใหม่ภายหลังหรือแจ้งผู้จัดทำไลน์แชทบอทด้วยจ้า');
		}
	}
	else {
		return BotReplyText('เกิดข้อผิดพลาด กรุณาลองใหม่ภายหลังหรือแจ้งผู้จัดทำไลน์แชทบอทด้วยจ้า');
	}
}
/**********************************************************************************************************************************/
//Function to insert data to postgresql database to easier than insert data to database by terminal
function InsertDataToDB() {
	$db = pg_connect($GLOBALS['pgsql_conn']);		

	$t = 'text';
	// $result = pg_query($db, "INSERT INTO tbhlinebotjokeq (question, answer) VALUES 
	// 					('หมูอะไร ไวกว่าหมูย่าง?', 'หมูวิ่ง'),
	// 					('อะไรเอ่ย ใหญ่กว่ารถสิบล้อ?', 'รถสิบล้อชุบแป้งทอด')
	// 					;");//,('คืนนี้แหล่ะ อยากได้กี่ครั้งหล่ะ', '12')

	$result = pg_query($db, "UPDATE tbhlinebotjokeq
							SET question = 'อะไรเอ่ย ใหญ่กว่ารถสิบล้อ'
							WHERE id = '2'");		
}