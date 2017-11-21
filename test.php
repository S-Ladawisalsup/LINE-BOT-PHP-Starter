<?php

$dsn = 'pgsql:'
	. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
	. 'dbname=dfusod038c3j35;'
	. 'user=mmbbbssobrmqjs;'
	. 'port=5432;'
	. 'sslmode=require;'
	. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

$db = new PDO($dsn);

// $one = 1;

$query_locnametemp = 'SELECT id, temperature, lastchangedatetime FROM tbhlinebottemploc ORDER BY id ASC';
$results = $db->query($query_locnametemp);
$last_temp = array();
$index = 0;
while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
    $last_temp[$index] = array();
    $last_temp[$index]['id'] = $row["id"];
	$last_temp[$index]['temp'] = htmlspecialchars($row["temperature"]);
	$last_temp[$index]['datetime'] = htmlspecialchars($row["lastchangedatetime"]);
	$index = $index + 1;
}
$results->closeCursor();

foreach ($last_temp as $final_temp) {
	if ($final_temp['id'] == $one) {
		$temp_new = array('temp'     => $final_temp['temp'], 
						  'datetime' => $final_temp['datetime']);
	}
}

echo $temp_new['temp'] . "C at " . $temp_new['datetime'];