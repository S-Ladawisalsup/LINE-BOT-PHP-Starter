<?php

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