<?php
include 'utilities.php';
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

	CheckBirthDay($db);
}
/**********************************************************************************************************************************/
function BotPush($room, $msg) {
	$access_token = 'CFecc4UnPdpCUxVk2VuTlf7ANCYHbCpaxYltjR/z15zMJ/KzsPIVrp4tCql4xmQYr8qgJSZ6oitEZ0/PKH+FpdneucSfPgjTP03mQ5KRSKqYT93fEEvGDqOUxJ/SBoS3oTXcJaRSxlPVBWxH+8PWxAdB04t89/1O/w1cDnyilFU=';

	$messages = BotReplyText($msg);
	
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

	if (!isset($bot_mod) || $bot_mod != 'allow') {
		$greeting = array('0' => 'สวัสดีตอนเช้าคับทุกคนนนนนน อากาศตอนเช้าสบายดีมั้ย ขอเสียงโหน่ยยยยยยย',
					  	  '1' => 'เช้าแล้วน้าาา ทุกคนตอนนี้เป็นไงกันบ้าง ขอเสียงโหน่ยยยยยยย',
					  	  '2' => 'ทำไมมันเงียบจังน้า ทำไมมันถึงเงียบกว่าชาวบ้านเค้า');
		return BotReplyText($greeting[rand(0, 2)]);
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

	return BotReplyText($joke_q . '?');
}
/**********************************************************************************************************************************/