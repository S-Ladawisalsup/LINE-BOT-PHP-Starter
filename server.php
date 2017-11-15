<?php

$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

$db = new PDO($dsn);

//$query = 'SELECT id, questiontext, questiontype, typename FROM tbhlinebotchkqa ORDER BY id ASC';
$query = 'UPDATE tbhlinebottemploc 
		SET (temperature, lastchangedatetime) = (' . $_POST["temperature"] . ', ' . $_POST["timestamp"] . ') 
		WHERE location = \'ITSD Room\''; 

$result = $db->query($query);

echo "Temperature is " . $_POST["temperature"] . ' at time ' . $_POST["timestamp"];