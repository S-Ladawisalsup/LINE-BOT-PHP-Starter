<?php
date_default_timezone_set("Asia/Bangkok");
$text = 'pwggezwpr007nn007xptzห้องโปรแกรมเมอร์';

$dsn = 'pgsql:'
	. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
	. 'dbname=dfusod038c3j35;'
	. 'user=mmbbbssobrmqjs;'
	. 'port=5432;'
	. 'sslmode=require;'
	. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

$db = new PDO($dsn);

$query_loccall = 'SELECT loc_callname, loc_id FROM tbhlinebotlocname ORDER BY id ASC';
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

$tempresult = $curr_place . 'ไม่มี' . $curr_locname . 'น๊ะจ๊ะ อยากรู้เดินไปดูเองเลยจ้า';
if ($curr_place != 0) {
	$db2 = new PDO($dsn);
	$query_locnametemp = "SELECT temperature, lastchangedatetime FROM tbhlinebottemploc WHERE id = $curr_place";
	$results = $db2->query($query_locnametemp);
	$last_temp = array();
	while ($row = $results->fetch(PDO::FETCH_ASSOC)) {  
		$last_temp['temp'] = htmlspecialchars($row["temperature"]);
		$last_temp['datetime'] = substr(htmlspecialchars($row["lastchangedatetime"]), 0, 16);
	}
	$results->closeCursor();

	if (substr($last_temp['datetime'], 0, 10) == date("Y-m-d")) {
		//lastchangedatetime == datenow, tell only time
		$last_temp['datetime'] = date("Y-m-d H:i", $last_temp['datetime']);
		$previous_time = substr($last_temp['datetime'], 11);
		$previous_time = str_replace(':', '.', $previous_time);
		$tempresult = 'เมื่อเวลา ' . $previous_time . 'น. อุณหภูมิที่' . $curr_locname . 'เท่ากับ ' . $last_temp['temp'] . ' องศาเซลเซียส จ้า';
	}
	else {
		//lastchangedatetime != datenow, tell date and time
		$last_temp['datetime'] = date("Y-m-d H:i", $last_temp['datetime']);
		$previous_date = date("d/m/Y", strtotime(substr($last_temp['datetime'], 0, 10)));
		$previous_time = substr($last_temp['datetime'], 11);
		$previous_time = str_replace(':', '.', $previous_time);
		$tempresult = 'เมื่อวันที่ ' . $previous_date . ' เวลา ' . $previous_time . 'น. อุณหภูมิที่' . $curr_locname . 'เท่ากับ ' . $last_temp['temp'] . ' องศาเซลเซียส จ้า';
	}
}
echo $tempresult;