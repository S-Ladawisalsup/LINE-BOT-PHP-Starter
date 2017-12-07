<?php
date_default_timezone_set("Asia/Bangkok");
// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);

if (!is_null($_POST['val']) || !is_null($events)) {
	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);
	$query = "SELECT user_id FROM tbhlinebotmem WHERE position != 'user'";
	$result = $db->query($query);

	$groups = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $groups[$index] = htmlspecialchars($row["user_id"]);
		$index = $index + 1;
	}
	$result->closeCursor();

	$greeting = array('0' => 'สวัสดีตอนเช้าคับทุกคนนนนนน อากาศตอนเช้าสบายดีมั้ย ขอเสียงโหน่ยยยยยยย',
					  '1' => 'เช้าแล้วน้าาา ทุกคนตอนนี้เป็นไงกันบ้าง ขอเสียงโหน่ยยยยยยย');
	
	foreach ($groups as $group) {
		BotPush($group, $greeting[rand(0, 10000) % 2]);
		//Maybe add random function if have to many groups registered. 
	}

	//will add hdb user here....
}
/**********************************************************************************************************************************/
function BotPush($room, $msg) {
	$access_token = 'CFecc4UnPdpCUxVk2VuTlf7ANCYHbCpaxYltjR/z15zMJ/KzsPIVrp4tCql4xmQYr8qgJSZ6oitEZ0/PKH+FpdneucSfPgjTP03mQ5KRSKqYT93fEEvGDqOUxJ/SBoS3oTXcJaRSxlPVBWxH+8PWxAdB04t89/1O/w1cDnyilFU=';

	$messages = [						
		'type' => 'text',
		'text' => $msg
	];
	
	// Make a POST Request to Messaging API to push to sender
	$url = 'https://api.line.me/v2/bot/message/push';
	$data = [
		'to' => $room,
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
function CheckBirthDay($db) {
	$query = "SELECT user_id, date_of_birth FROM tbhlinebotmem WHERE position = 'user'";
	$result = $db->query($query);

	$bd = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$bd = array();
	    $bd[$index]['uid'] = htmlspecialchars($row["user_id"]);
	    $bd[$index]['bd'] = htmlspecialchars($row["date_of_birth"]);
		$index = $index + 1;
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
  			$user = $user + 1;
		}
	}

	if (!empty($born)) {
		foreach ($born as $item) {
			BotPush($item['uid'], CreateMsg($item['age']));
		}
	}
}
/**********************************************************************************************************************************/
function CreateMsg($age) {
	$prefix = array('0' => 'ยินดีด้วยน้าาา อายุ', '1' => 'อุ๊ย อายุ', '2' => 'โหวววว อายุ', '3' => 'สุขสันต์วันเกิดนะครับ อายุ');
	$wish = array('0' => ' แล้ว ขอให้มีความสุขมากๆนะ ขอให้สุขสมหวังในสิ่งที่อยากได้นะ สุขสันต์วันเกิดนะ',
				  '1' => ' แล้ว สุขสันต์วันเกิดนะ ขอให้โชคดีมีชัย คิดสิ่งหนึ่งสิ่งใด ขอให้สมปรารถนาครับ',
				  '2' => ' แล้ว ขอให้มีความสุขมากๆ สุขสันต์วันเกิดนะ ปะ!! วันนี้ฉลองไหนดี',
				  '3' => ' แล้ว แม้ไม่มีของขวัญให้ แต่ก็ขอให้มีความสุข ขอให้ได้รับแต่สิ่งดีๆเข้ามาในชีวิตนะครับ',
				  '4' => ' ปีแล้ว ขอให้มีสุขภาพแข็งแรงเสมอ มีความสุขในชีวิตนะครับ',
				  '5' => ' ปีแล้ว ขอพรจากสิ่งศักดิ์สิทธิ์ทั้งหลาย จงอวยชัยให้ท่านมีความสุขในวันเกิดและตลอดไปด้วยเถิด',
				  '6' => ' ปี ครบรอบวันสุดมงคลอีกแล้ว กับวันดีวันนี้ สุขสันต์วันเกิดครับท่าน',
				  '7' => ' ปีแล้ว แต่นึกว่ายังเด็กว่านี้อีก 10-20ปี เลยนะครับนี่ ขอให้มีสุขภาพแข็งแรง พบเจอแต่สิ่งดีๆนะครับ');
	if ($age <= 25) {
		
	}
	else if ($age > 25 && $age <= 40) {
		# code...
	}
	else {
		# code...
	}
	return '555';
}