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

$dtm_new = date_create_from_format("Y-m-d H:i");

echo $dtm_new . '<br />datetime type is : ' . gettype($dtm_new) . '<br />datetime now is ' . date("Y-m-d H:i");