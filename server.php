<?php

// Cannot debug to print show in page
// But will echo response to root source data is sent
// if (!is_null($_POST["temperature"])) {
// 	TestWriteTempToDB($_POST["temperature"]);
// 	echo "Temperature is " . $_POST["temperature"];
// }
// else {
// 	echo "Cannot receive any data";
// }

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);

// // Validate parsed JSON data
// if (!is_null($events)) {
// 	// echo $events['event']['temperature'];
// 	// foreach ($events['event']['server'] as $event) {
// 	// 	echo '/' . $event['name'] . ':' $event['status'];
// 	// }
// 	echo "Hello World!";
// }
// else {
// 	echo "Cannot receive any data/".$content."/".$events."/".$events['temperature']."/".$events['server'];
// }

echo "content : ".$content."\r\nevents temperature : ".$events['event']['temperature']."\r\nevent server : ".$events['event']['server'];

/**********************************************************************************************************************************/
function TestWriteTempToDB($curr_temperature) {

	$db = pg_connect("host=ec2-54-243-187-133.compute-1.amazonaws.com 
					port=5432 
					dbname=dfusod038c3j35 
					user=mmbbbssobrmqjs 
					password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa");

	$result = pg_query($db, "UPDATE tbhlinebottemploc 
							SET temperature = $curr_temperature
							WHERE location = 'ITSD Room'");				
}