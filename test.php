<?php

$dsn = 'pgsql:'
	. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
	. 'dbname=dfusod038c3j35;'
	. 'user=mmbbbssobrmqjs;'
	. 'port=5432;'
	. 'sslmode=require;'
	. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

$db = new PDO($dsn);

$one = 1;

$query_locnametemp = "SELECT temperature, lastchangedatetime FROM tbhlinebottemploc WHERE id = $one";
$results = $db->query($query_locnametemp);
$last_temp = array();
while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
	$last_temp['temp'] = htmlspecialchars($row["temperature"]);
	$last_temp['datetime'] = substr(htmlspecialchars($row["lastchangedatetime"]), 0, 16) ;
}
$results->closeCursor();

$last_temp['datetime'] = '2017-11-21';

if (substr($last_temp['datetime'], 0, 10) == date("Y-m-d")) {
	echo substr($last_temp['datetime'], 0, 10) . ' / ' . date("Y-m-d");
}
else {
	echo substr($last_temp['datetime'], 0, 10) . ' | ' . date("Y-m-d");
}