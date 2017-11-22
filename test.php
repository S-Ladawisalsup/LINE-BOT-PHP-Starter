<?php

$state = true;

if ($state) {
	$curr_temperature = 30;
	$location = 'ITSD Room';
	UpdateTempToDB($curr_temperature, $location);
	$state = false;
}

echo "<br />Update successful";

function UpdateTempToDB($curr_temperature, $location) {
	$db = pg_connect("host=ec2-54-243-187-133.compute-1.amazonaws.com 
					port=5432 
					dbname=dfusod038c3j35 
					user=mmbbbssobrmqjs 
					password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa");

	echo 'temperature is ' . $curr_temperature . ' in ' . $location;

	$result = pg_query($db, "UPDATE tbhlinebottemploc 
							SET temperature = $curr_temperature
							WHERE location = $location");				
}